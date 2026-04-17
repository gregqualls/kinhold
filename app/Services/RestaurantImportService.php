<?php

namespace App\Services;

use App\Enums\TagScope;
use App\Models\Family;
use App\Models\FamilyRestaurant;
use App\Models\Restaurant;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RestaurantImportService
{
    /**
     * Import a restaurant from a Google Maps URL.
     *
     * Resolves short URLs, scrapes JSON-LD and OG meta tags,
     * then creates or finds the restaurant record.
     */
    public function importFromUrl(string $url, Family $family): Restaurant
    {
        $data = $this->extractFromUrl($url);

        // Determine the dedup key based on URL type
        $googleMapsUrl = $data['google_maps_url'] ?? null;
        $menuUrl = $data['menu_url'] ?? null;

        // Try to find existing restaurant by either URL
        $restaurant = null;
        if ($googleMapsUrl) {
            $restaurant = Restaurant::where('google_maps_url', $googleMapsUrl)->first();
        }
        if (! $restaurant && $menuUrl) {
            $restaurant = Restaurant::where('menu_url', $menuUrl)->first();
        }

        if (! $restaurant) {
            $restaurant = Restaurant::create([
                'name' => $data['name'] ?? $this->extractNameFromUrl($googleMapsUrl ?? $menuUrl ?? $url),
                'google_maps_url' => $googleMapsUrl,
                'menu_url' => $menuUrl,
                'address' => $data['address'] ?? null,
                'phone' => $data['phone'] ?? null,
                'image_url' => $data['image_url'] ?? null,
            ]);
        }

        // Update fields if the restaurant existed but had missing data
        $updates = [];
        foreach (['image_url', 'address', 'phone'] as $field) {
            if (! $restaurant->$field && ! empty($data[$field])) {
                $updates[$field] = $data[$field];
            }
        }
        if ($updates) {
            $restaurant->update($updates);
        }

        // Link to family if not already linked
        FamilyRestaurant::firstOrCreate(
            ['family_id' => $family->id, 'restaurant_id' => $restaurant->id]
        );

        // Attach cuisine tags (scoped to this family) to the restaurant.
        if (! empty($data['cuisines'])) {
            $this->attachCuisineTags($restaurant, $family, $data['cuisines']);
        }

        return $restaurant;
    }

    /**
     * Find-or-create a food-scoped tag per cuisine name for the family and
     * attach them to the restaurant. Idempotent.
     *
     * @param  array<int, string>  $cuisines
     */
    public function attachCuisineTags(Restaurant $restaurant, Family $family, array $cuisines): void
    {
        $tagIds = [];
        foreach ($cuisines as $name) {
            $trimmed = trim($name);
            if ($trimmed === '') {
                continue;
            }
            $tag = Tag::firstOrCreate(
                [
                    'family_id' => $family->id,
                    'name' => $trimmed,
                    'scope' => TagScope::Food->value,
                ],
                [
                    'sort_order' => (Tag::where('family_id', $family->id)->max('sort_order') ?? 0) + 1,
                ]
            );
            $tagIds[] = $tag->id;
        }

        if ($tagIds) {
            $restaurant->tags()->syncWithoutDetaching($tagIds);
        }
    }

    /**
     * Store an uploaded restaurant photo and return its relative path.
     */
    public function storeUploadedImage(UploadedFile $photo): string
    {
        $filename = Str::uuid()->toString().'.'.$photo->guessExtension();

        return Storage::disk('public')->putFileAs('restaurants', $photo, $filename);
    }

    /**
     * Download a remote image and store it locally. Returns relative path or null.
     * SSRF-protected: validates scheme, pins DNS to public IP.
     */
    public function downloadAndStoreImage(string $imageUrl): ?string
    {
        try {
            $this->assertUrlIsSafe($imageUrl);

            $parsed = parse_url($imageUrl);
            $host = $parsed['host'];
            $scheme = $parsed['scheme'];
            $port = $parsed['port'] ?? ($scheme === 'http' ? 80 : 443);

            $ip = gethostbyname($host);
            if ($ip === $host || ! $this->isPublicIp($ip)) {
                return null;
            }

            $response = Http::withOptions([
                'timeout' => 10,
                'resolve' => ["{$host}:{$port}:{$ip}"],
            ])->withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            ])->get($imageUrl);

            if ($response->failed()) {
                return null;
            }

            // Refuse images over 5 MB to prevent disk exhaustion
            if (strlen($response->body()) > 5_242_880) {
                return null;
            }

            $contentType = $response->header('Content-Type') ?: 'image/jpeg';
            $ext = match (true) {
                str_contains($contentType, 'png') => 'png',
                str_contains($contentType, 'gif') => 'gif',
                str_contains($contentType, 'webp') => 'webp',
                default => 'jpg',
            };

            $filename = Str::uuid()->toString().'.'.$ext;
            Storage::disk('public')->put('restaurants/'.$filename, $response->body());

            return 'restaurants/'.$filename;
        } catch (\Throwable $e) {
            Log::warning('RestaurantImportService: Failed to download image', [
                'url' => $imageUrl,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Search global restaurant database.
     */
    public function search(string $query, int $limit = 20): Collection
    {
        return Restaurant::where('name', 'ILIKE', "%{$query}%")
            ->orderBy('name')
            ->limit($limit)
            ->get();
    }

    /**
     * Fetch a URL with redirect following and SSRF protection.
     * Returns [finalUrl, html] or throws on failure.
     *
     * SSRF defenses:
     * - Only http/https schemes allowed
     * - DNS resolved and validated as public IP
     * - DNS pinned with Guzzle 'resolve' option (prevents DNS rebinding)
     * - Redirects manually followed and re-validated at each hop
     */
    private function fetchWithRedirects(string $url): array
    {
        $maxRedirects = 5;
        $currentUrl = $url;

        for ($i = 0; $i <= $maxRedirects; $i++) {
            $this->assertUrlIsSafe($currentUrl);

            $parsed = parse_url($currentUrl);
            $host = $parsed['host'];
            $scheme = $parsed['scheme'];
            $port = $parsed['port'] ?? ($scheme === 'http' ? 80 : 443);

            $ip = gethostbyname($host);
            if ($ip === $host || ! $this->isPublicIp($ip)) {
                throw new \RuntimeException("URL resolves to a non-public address: {$host}");
            }

            $response = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                'Accept-Language' => 'en-US,en;q=0.9',
            ])->withOptions([
                // Pin DNS to the validated IP to prevent rebinding
                'resolve' => ["{$host}:{$port}:{$ip}"],
                // Disable automatic redirects so we can re-validate each hop
                'allow_redirects' => false,
                'timeout' => 15,
                'connect_timeout' => 10,
            ])->get($currentUrl);

            $status = $response->status();

            // Redirect → loop with new URL (after validating it)
            if (in_array($status, [301, 302, 303, 307, 308], true)) {
                $location = $response->header('Location');
                if (! $location) {
                    throw new \RuntimeException('Redirect without Location header');
                }

                // Resolve relative redirects against the current URL
                if (! preg_match('#^https?://#i', $location)) {
                    $base = $scheme.'://'.$host.($port && $port !== 80 && $port !== 443 ? ':'.$port : '');
                    $location = str_starts_with($location, '/') ? $base.$location : $base.'/'.$location;
                }

                $currentUrl = $location;

                continue;
            }

            if ($response->failed()) {
                throw new \RuntimeException("Failed to fetch URL: HTTP {$status}");
            }

            return [$currentUrl, $response->body()];
        }

        throw new \RuntimeException('Too many redirects');
    }

    /**
     * Validate a URL is safe to fetch (http/https, public host, no SSRF vectors).
     */
    private function assertUrlIsSafe(string $url): void
    {
        $parsed = parse_url($url);

        if (! $parsed || empty($parsed['scheme']) || empty($parsed['host'])) {
            throw new \RuntimeException('Invalid URL');
        }

        $scheme = strtolower($parsed['scheme']);
        if (! in_array($scheme, ['http', 'https'], true)) {
            throw new \RuntimeException("Unsupported URL scheme: {$scheme}");
        }
    }

    /**
     * Check if an IP address is public (not private, not reserved, not loopback).
     */
    private function isPublicIp(string $ip): bool
    {
        return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false;
    }

    /**
     * Detect if a URL is a Google Maps link (including short URLs).
     */
    private function isGoogleMapsUrl(string $url): bool
    {
        return (bool) preg_match('#(maps\.app\.goo\.gl|google\.\w+/maps|goo\.gl/maps|maps\.google)#i', $url);
    }

    /**
     * Extract restaurant data from any URL by following redirects and scraping.
     * Supports Google Maps URLs and restaurant website URLs.
     * Public so the controller can use it for preview mode.
     */
    public function extractFromUrl(string $url): array
    {
        $data = [];
        $isGoogleMaps = $this->isGoogleMapsUrl($url);

        try {
            [$finalUrl, $html] = $this->fetchWithRedirects($url);

            // Determine if the resolved URL is Google Maps
            if (! $isGoogleMaps) {
                $isGoogleMaps = $this->isGoogleMapsUrl($finalUrl);
            }

            if ($isGoogleMaps) {
                $data['google_maps_url'] = $finalUrl;
            } else {
                // It's a restaurant website — store the URL as menu_url
                $data['menu_url'] = $finalUrl;
            }

            // 1. Google Maps: parse place name from URL path
            if ($isGoogleMaps && preg_match('#/place/([^/@]+)#', $finalUrl, $matches)) {
                $data['name'] = urldecode(str_replace('+', ' ', $matches[1]));
            }

            // 2. Parse JSON-LD structured data (same approach as RecipeImportService)
            $jsonLd = $this->parseJsonLd($html);
            if ($jsonLd) {
                if (! empty($jsonLd['name'])) {
                    $data['name'] = $jsonLd['name'];
                }
                if (! empty($jsonLd['telephone'])) {
                    $data['phone'] = $jsonLd['telephone'];
                }
                if (! empty($jsonLd['url']) && empty($data['menu_url'])) {
                    $data['menu_url'] = $jsonLd['url'];
                }
                if (! empty($jsonLd['image'])) {
                    $image = is_array($jsonLd['image']) ? ($jsonLd['image'][0] ?? null) : $jsonLd['image'];
                    if ($image && ! $this->isStaticMapImage($image)) {
                        $data['image_url'] = $image;
                    }
                }
                if (! empty($jsonLd['address'])) {
                    $addr = $jsonLd['address'];
                    if (is_array($addr)) {
                        $data['address'] = implode(', ', array_filter([
                            $addr['streetAddress'] ?? null,
                            $addr['addressLocality'] ?? null,
                            $addr['addressRegion'] ?? null,
                            $addr['postalCode'] ?? null,
                        ]));
                    } elseif (is_string($addr)) {
                        $data['address'] = $addr;
                    }
                }
                if (! empty($jsonLd['servesCuisine'])) {
                    $raw = is_array($jsonLd['servesCuisine'])
                        ? $jsonLd['servesCuisine']
                        : preg_split('/[,;]+/', (string) $jsonLd['servesCuisine']);
                    $data['cuisines'] = array_values(array_filter(array_map('trim', $raw)));
                }
            }

            // 3. Open Graph meta tags (fallback for name, image, description)
            $ogData = $this->parseOpenGraphTags($html);
            if (empty($data['name']) && ! empty($ogData['title'])) {
                // Split by separators, take the first non-generic part
                $parts = preg_split('/\s*[-–|·]\s*/', $ogData['title']);
                foreach ($parts as $part) {
                    $part = trim($part);
                    if ($part && ! $this->isGenericTitle($part)) {
                        $data['name'] = $part;
                        break;
                    }
                }
            }
            if (empty($data['image_url']) && ! empty($ogData['image']) && ! $this->isStaticMapImage($ogData['image'])) {
                $data['image_url'] = html_entity_decode($ogData['image'], ENT_QUOTES, 'UTF-8');
            }
            if (empty($data['address']) && ! empty($ogData['description']) && ! $this->isGenericTitle($ogData['description'])) {
                // Only use OG description as address if it looks like one (short, has numbers)
                $desc = $ogData['description'];
                if (strlen($desc) < 200 && preg_match('/\d/', $desc)) {
                    $data['address'] = $desc;
                }
            }

            // 4. Fallback: extract phone from tel: links or structured data in page
            if (empty($data['phone'])) {
                if (preg_match('/href=["\']tel:([^"\']+)/i', $html, $m)) {
                    $data['phone'] = html_entity_decode(trim($m[1]), ENT_QUOTES, 'UTF-8');
                } elseif (preg_match('/"telephone"\s*:\s*"([^"]+)"/', $html, $m)) {
                    $data['phone'] = html_entity_decode($m[1], ENT_QUOTES, 'UTF-8');
                }
            }

            // 5. Fallback: HTML <title> tag for name
            if (empty($data['name'])) {
                if (preg_match('/<title>([^<]+)<\/title>/i', $html, $m)) {
                    $title = trim($m[1]);
                    // Try the full title first, then try parts split by separators
                    // "The Hesketh | Home" → try "The Hesketh", "Home"
                    // "Home - Star Pubs" → try "Home", "Star Pubs"
                    $parts = preg_split('/\s*[-–|·]\s*/', $title);
                    $bestName = null;
                    foreach ($parts as $part) {
                        $part = trim($part);
                        if ($part && ! $this->isGenericTitle($part)) {
                            $bestName = $part;
                            break;
                        }
                    }
                    // If the first non-generic part was found, use it
                    if ($bestName) {
                        $data['name'] = $bestName;
                    }
                }
            }

            // 6. Last resort: derive name from domain
            if (empty($data['name'])) {
                $host = parse_url($finalUrl, PHP_URL_HOST) ?? '';
                // "www.thehesketh.co.uk" → "thehesketh"
                $domain = preg_replace('/^www\./', '', $host);
                $domain = preg_replace('/\.(com|co\.uk|net|org|io|app|restaurant|cafe|pub|bar).*$/', '', $domain);
                if ($domain && strlen($domain) > 2) {
                    $data['name'] = ucwords(str_replace(['-', '_', '.'], ' ', $domain));
                }
            }

            // 7. Google Maps: try to find embedded place photos
            if ($isGoogleMaps && empty($data['image_url'])) {
                $data['image_url'] = $this->extractPlacePhoto($html);
            }

            // 8. Download the image locally so it persists and the preview can show it
            if (! empty($data['image_url'])) {
                $localPath = $this->downloadAndStoreImage($data['image_url']);
                if ($localPath) {
                    // Replace remote URL with the local storage URL
                    $data['image_url'] = '/storage/'.$localPath;
                }
                // If download failed, keep the remote URL as fallback
            }

        } catch (\Throwable $e) {
            Log::warning('Restaurant URL scraping failed', [
                'url' => $url,
                'error' => $e->getMessage(),
            ]);
        }

        return $data;
    }

    /**
     * Parse JSON-LD structured data from HTML — same approach as RecipeImportService.
     */
    private function parseJsonLd(string $html): ?array
    {
        preg_match_all('/<script[^>]+type=["\']application\/ld\+json["\'][^>]*>(.*?)<\/script>/si', $html, $matches);

        foreach ($matches[1] as $raw) {
            $decoded = json_decode(trim($raw), true);

            if (! is_array($decoded)) {
                continue;
            }

            // Handle @graph arrays
            if (isset($decoded['@graph'])) {
                foreach ($decoded['@graph'] as $item) {
                    if ($this->isLocalBusinessType($item)) {
                        return $item;
                    }
                }
            }

            // Direct match
            if ($this->isLocalBusinessType($decoded)) {
                return $decoded;
            }
        }

        return null;
    }

    /**
     * Check if a JSON-LD item is a restaurant/local business type.
     */
    private function isLocalBusinessType(array $item): bool
    {
        $type = $item['@type'] ?? '';
        if (is_array($type)) {
            $type = implode(' ', $type);
        }

        $businessTypes = [
            'Restaurant', 'FoodEstablishment', 'LocalBusiness',
            'CafeOrCoffeeShop', 'BarOrPub', 'FastFoodRestaurant',
            'Bakery', 'IceCreamShop', 'Pizzeria',
        ];

        foreach ($businessTypes as $bType) {
            if (stripos($type, $bType) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if a title is generic (not a real place/restaurant name).
     */
    private function isGenericTitle(string $title): bool
    {
        $generic = [
            'Google Maps', 'Google', 'Maps', '',
            'Home', 'Welcome', 'Homepage', 'Main',
            'Menu', 'Contact', 'About', 'About Us',
        ];

        return in_array(trim($title), $generic, true);
    }

    /**
     * Check if an image URL is a Google Maps static map tile.
     */
    private function isStaticMapImage(string $url): bool
    {
        return str_contains($url, 'maps/api/staticmap')
            || str_contains($url, 'maps.googleapis.com/maps/api/staticmap');
    }

    /**
     * Try to extract a place photo URL from Google's embedded page data.
     */
    private function extractPlacePhoto(string $html): ?string
    {
        // Google user-content CDN (place photos)
        if (preg_match('#(https://lh[35]\.googleusercontent\.com/[a-zA-Z0-9_/=\-]+)#', $html, $m)) {
            return $m[1];
        }

        if (preg_match('#(https://streetviewpixels-pa\.googleapis\.com/[^\s"\'<>]+)#', $html, $m)) {
            return html_entity_decode($m[1], ENT_QUOTES, 'UTF-8');
        }

        return null;
    }

    /**
     * Parse Open Graph + Twitter Card meta tags from HTML content.
     * Twitter/social images often have better quality than favicons or thumbnails.
     */
    private function parseOpenGraphTags(string $html): array
    {
        $og = [];

        // Primary: Open Graph
        $ogTags = [
            'title' => 'og:title',
            'description' => 'og:description',
            'image' => 'og:image',
        ];

        foreach ($ogTags as $key => $property) {
            $value = $this->findMetaTagContent($html, $property);
            if ($value !== null) {
                $og[$key] = $value;
            }
        }

        // Fallback: og:image:secure_url (sometimes higher-res)
        if (empty($og['image'])) {
            $secureImage = $this->findMetaTagContent($html, 'og:image:secure_url');
            if ($secureImage) {
                $og['image'] = $secureImage;
            }
        }

        // Fallback: Twitter Card image (often the best social share image)
        if (empty($og['image'])) {
            foreach (['twitter:image', 'twitter:image:src'] as $twitterProp) {
                $twitterImage = $this->findMetaTagContent($html, $twitterProp);
                if ($twitterImage) {
                    $og['image'] = $twitterImage;
                    break;
                }
            }
        }

        // Fallback: Twitter Card title/description
        if (empty($og['title'])) {
            $twTitle = $this->findMetaTagContent($html, 'twitter:title');
            if ($twTitle) {
                $og['title'] = $twTitle;
            }
        }
        if (empty($og['description'])) {
            $twDesc = $this->findMetaTagContent($html, 'twitter:description');
            if ($twDesc) {
                $og['description'] = $twDesc;
            }
        }

        return $og;
    }

    /**
     * Find a meta tag's content attribute, handling both attribute orderings.
     */
    private function findMetaTagContent(string $html, string $property): ?string
    {
        $escaped = preg_quote($property, '/');
        if (preg_match(
            '/<meta\s+(?:[^>]*?)(?:property|name)=["\']'.$escaped.'["\']\s+content=["\']([^"\']+)["\']/i',
            $html,
            $m
        )) {
            return html_entity_decode($m[1], ENT_QUOTES, 'UTF-8');
        }
        if (preg_match(
            '/<meta\s+content=["\']([^"\']+)["\']\s+(?:property|name)=["\']'.$escaped.'["\']/i',
            $html,
            $m
        )) {
            return html_entity_decode($m[1], ENT_QUOTES, 'UTF-8');
        }

        return null;
    }

    /**
     * Extract a reasonable name from a URL as fallback.
     */
    private function extractNameFromUrl(string $url): string
    {
        if (preg_match('#/place/([^/@]+)#', $url, $matches)) {
            return urldecode(str_replace('+', ' ', $matches[1]));
        }

        $parsed = parse_url($url);

        return $parsed['host'] ?? 'Imported Restaurant';
    }
}
