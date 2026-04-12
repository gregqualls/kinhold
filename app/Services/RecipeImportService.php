<?php

namespace App\Services;

use App\Models\Family;
use App\Models\Recipe;
use App\Models\User;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\HttpException;

class RecipeImportService
{
    private const ANTHROPIC_API_URL = 'https://api.anthropic.com/v1/messages';

    private const ANTHROPIC_VERSION = '2023-06-01';

    private const MAX_TOKENS = 2048;

    private const URL_SYSTEM_PROMPT = <<<'PROMPT'
You are a recipe extraction assistant. Extract a structured recipe from the following web page content.

Return a JSON object with EXACTLY these fields:
{
  "title": "Recipe title (string, required)",
  "description": "Short description (string, optional)",
  "servings": "Number of servings (integer, optional)",
  "prep_time": "Prep time in minutes (integer, optional)",
  "cook_time": "Cook time in minutes (integer, optional)",
  "ingredients": [
    {
      "name": "Ingredient name (string, required)",
      "quantity": "Numeric amount as string (string, optional)",
      "unit": "Unit of measure (string, optional)",
      "preparation": "Prep instructions like 'diced' or 'minced' (string, optional)"
    }
  ],
  "instructions": ["Step 1 text", "Step 2 text", "..."]
}

Rules:
- Extract ONLY recipe information. Ignore ads, navigation, comments.
- If a field is not found, set it to null (except title and instructions which are required).
- Separate ingredient preparation from the ingredient name (e.g., "2 cups diced onion" → name: "onion", quantity: "2", unit: "cups", preparation: "diced").
- Instructions should be clean step strings without numbering prefixes.
- Return valid JSON only. No markdown, no explanation.
PROMPT;

    private const PHOTO_PROMPT = <<<'PROMPT'
Extract a structured recipe from this image. Return JSON with:
{
  "title": "Recipe title",
  "description": "Short description",
  "servings": 4,
  "prep_time": 15,
  "cook_time": 30,
  "ingredients": [
    {"name": "ingredient", "quantity": "2", "unit": "cups", "preparation": "diced"}
  ],
  "instructions": ["Step 1", "Step 2"]
}

If any field cannot be determined from the image, set it to null.
Return valid JSON only.
PROMPT;

    public function __construct(private RecipeService $recipeService) {}

    /**
     * Import a recipe from a URL.
     *
     * @return array<string, mixed>
     */
    public function importFromUrl(string $url, Family $family, User $user, bool $preview = false): array
    {
        $html = $this->fetchUrl($url);

        $data = $this->parseJsonLd($html);

        if ($data === null) {
            $data = $this->extractViaLlm($html, $family);
        }

        $this->validateExtracted($data);

        $data['source_url'] = $url;
        $data['source_type'] = 'url';

        // Check for duplicate source_url within this family
        $existing = Recipe::where('family_id', $family->id)
            ->where('source_url', $url)
            ->first();

        if ($existing) {
            $data['duplicate_of'] = $existing->id;
            $data['duplicate_title'] = $existing->title;
        }

        if ($preview) {
            return $data;
        }

        return $this->persistImport($data, $family, $user);
    }

    /**
     * Import a recipe from a photo.
     *
     * @return array<string, mixed>
     */
    public function importFromPhoto(UploadedFile $photo, Family $family, User $user, bool $preview = false): array
    {
        $data = $this->extractFromPhoto($photo, $family);

        $this->validateExtracted($data);

        $data['source_type'] = 'photo';

        if ($preview) {
            return $data;
        }

        // Store the image before persisting
        $imagePath = $this->storeRecipeImage($photo);
        $data['image_path'] = $imagePath;

        return $this->persistImport($data, $family, $user);
    }

    /**
     * Fetch raw HTML from a URL with SSRF protection.
     *
     * Resolves DNS once and validates the IP before making the request.
     * The request is then pinned to the validated IP via cURL's RESOLVE option
     * to prevent DNS rebinding (TOCTOU) attacks.
     */
    private function fetchUrl(string $url): string
    {
        $parsed = parse_url($url);
        $host = $parsed['host'] ?? '';
        $port = $parsed['port'] ?? ($parsed['scheme'] === 'http' ? 80 : 443);

        // Resolve DNS once and validate the IP
        $ip = gethostbyname($host);
        if ($ip === $host) {
            // gethostbyname returns the input if resolution fails
            throw new HttpException(422, "Couldn't fetch that URL. Check the link and try again.");
        }

        if (! $this->isPublicIp($ip)) {
            throw new HttpException(422, "Couldn't fetch that URL. Check the link and try again.");
        }

        try {
            // Pin DNS resolution to the validated IP to prevent rebinding
            $response = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36',
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            ])->withOptions([
                'resolve' => ["{$host}:{$port}:{$ip}"],
            ])->timeout(15)->get($url);
        } catch (ConnectionException $e) {
            throw new HttpException(422, "Couldn't fetch that URL. Check the link and try again.");
        }

        if ($response->failed()) {
            throw new HttpException(422, "Couldn't fetch that URL. Check the link and try again.");
        }

        return $response->body();
    }

    /**
     * Attempt to parse schema.org/Recipe JSON-LD from HTML.
     *
     * @return array<string, mixed>|null
     */
    private function parseJsonLd(string $html): ?array
    {
        preg_match_all('/<script[^>]+type=["\']application\/ld\+json["\'][^>]*>(.*?)<\/script>/si', $html, $matches);

        foreach ($matches[1] as $raw) {
            $decoded = json_decode(trim($raw), true);

            if (! is_array($decoded)) {
                continue;
            }

            // Handle @graph wrapper
            $candidates = isset($decoded['@graph']) ? $decoded['@graph'] : [$decoded];

            foreach ($candidates as $item) {
                $type = $item['@type'] ?? '';
                if (is_array($type)) {
                    $type = implode(',', $type);
                }

                if (stripos($type, 'Recipe') !== false) {
                    return $this->mapJsonLdToInternal($item);
                }
            }
        }

        return null;
    }

    /**
     * Map schema.org Recipe fields to our internal format.
     *
     * @param  array<string, mixed>  $item
     * @return array<string, mixed>
     */
    private function mapJsonLdToInternal(array $item): array
    {
        $ingredients = [];
        foreach ($item['recipeIngredient'] ?? [] as $line) {
            $ingredients[] = $this->parseIngredientString((string) $line);
        }

        $instructions = [];
        $rawInstructions = $item['recipeInstructions'] ?? [];

        if (is_string($rawInstructions)) {
            $instructions = array_values(array_filter(array_map('trim', explode("\n", $rawInstructions))));
        } elseif (is_array($rawInstructions)) {
            foreach ($rawInstructions as $step) {
                if (is_string($step)) {
                    $instructions[] = trim($step);
                } elseif (is_array($step)) {
                    // HowToStep or HowToSection
                    if (isset($step['itemListElement'])) {
                        foreach ($step['itemListElement'] as $subStep) {
                            $text = $subStep['text'] ?? $subStep['name'] ?? '';
                            if ($text !== '') {
                                $instructions[] = trim((string) $text);
                            }
                        }
                    } else {
                        $text = $step['text'] ?? $step['name'] ?? '';
                        if ($text !== '') {
                            $instructions[] = trim((string) $text);
                        }
                    }
                }
            }
        }

        return [
            'title' => $item['name'] ?? null,
            'description' => isset($item['description']) ? strip_tags((string) $item['description']) : null,
            'servings' => $this->parseServings($item['recipeYield'] ?? null),
            'prep_time' => $this->parseIsoDuration($item['prepTime'] ?? null),
            'cook_time' => $this->parseIsoDuration($item['cookTime'] ?? null),
            'ingredients' => $ingredients,
            'instructions' => array_values(array_filter($instructions)),
        ];
    }

    /**
     * Parse a plain ingredient string into structured fields.
     *
     * @return array<string, string|null>
     */
    private function parseIngredientString(string $line): array
    {
        return [
            'name' => $line,
            'quantity' => null,
            'unit' => null,
            'preparation' => null,
        ];
    }

    /**
     * Parse ISO 8601 duration (e.g. PT30M, PT1H30M) to integer minutes.
     */
    private function parseIsoDuration(mixed $value): ?int
    {
        if (empty($value)) {
            return null;
        }

        $str = (string) $value;

        if (preg_match('/PT(?:(\d+)H)?(?:(\d+)M)?/', $str, $m)) {
            $hours = (int) ($m[1] ?? 0);
            $minutes = (int) ($m[2] ?? 0);
            $total = ($hours * 60) + $minutes;

            return $total > 0 ? $total : null;
        }

        return null;
    }

    /**
     * Parse recipeYield to an integer servings count.
     */
    private function parseServings(mixed $value): ?int
    {
        if (empty($value)) {
            return null;
        }

        if (is_array($value)) {
            $value = $value[0] ?? '';
        }

        preg_match('/\d+/', (string) $value, $m);

        return isset($m[0]) ? (int) $m[0] : null;
    }

    /**
     * Strip non-recipe HTML and call Claude to extract structured data.
     *
     * @return array<string, mixed>
     */
    private function extractViaLlm(string $html, Family $family): array
    {
        $cleaned = $this->stripNonContentHtml($html);
        // Truncate to ~10k chars to avoid huge token counts
        $cleaned = mb_substr($cleaned, 0, 10000);

        $apiKey = $this->resolveApiKey($family);
        $model = $this->resolveModel($family);

        $response = Http::withHeaders([
            'x-api-key' => $apiKey,
            'anthropic-version' => self::ANTHROPIC_VERSION,
            'Content-Type' => 'application/json',
        ])->timeout(60)->post(self::ANTHROPIC_API_URL, [
            'model' => $model,
            'max_tokens' => self::MAX_TOKENS,
            'system' => self::URL_SYSTEM_PROMPT,
            'messages' => [
                ['role' => 'user', 'content' => $cleaned],
            ],
        ]);

        if ($response->failed()) {
            Log::error('RecipeImportService: Anthropic API error (URL import)', [
                'status' => $response->status(),
            ]);
            throw new HttpException(500, 'Something went wrong with recipe extraction. Please try again.');
        }

        return $this->parseLlmResponse($response->json());
    }

    /**
     * Send photo to Claude Vision and extract recipe data.
     *
     * @return array<string, mixed>
     */
    private function extractFromPhoto(UploadedFile $photo, Family $family): array
    {
        $apiKey = $this->resolveApiKey($family);
        $model = $this->resolveModel($family);

        $base64 = base64_encode(file_get_contents($photo->getRealPath()));
        $mediaType = $photo->getMimeType() ?? 'image/jpeg';

        // HEIC isn't directly supported by Claude Vision — treat as jpeg
        if (in_array($mediaType, ['image/heic', 'image/heif'])) {
            $mediaType = 'image/jpeg';
        }

        $response = Http::withHeaders([
            'x-api-key' => $apiKey,
            'anthropic-version' => self::ANTHROPIC_VERSION,
            'Content-Type' => 'application/json',
        ])->timeout(60)->post(self::ANTHROPIC_API_URL, [
            'model' => $model,
            'max_tokens' => self::MAX_TOKENS,
            'messages' => [
                [
                    'role' => 'user',
                    'content' => [
                        [
                            'type' => 'image',
                            'source' => [
                                'type' => 'base64',
                                'media_type' => $mediaType,
                                'data' => $base64,
                            ],
                        ],
                        [
                            'type' => 'text',
                            'text' => self::PHOTO_PROMPT,
                        ],
                    ],
                ],
            ],
        ]);

        if ($response->failed()) {
            Log::error('RecipeImportService: Anthropic API error (photo import)', [
                'status' => $response->status(),
            ]);
            throw new HttpException(500, 'Something went wrong with recipe extraction. Please try again.');
        }

        return $this->parseLlmResponse($response->json());
    }

    /**
     * Parse the Anthropic API response and decode the JSON recipe.
     *
     * @param  array<string, mixed>  $responseData
     * @return array<string, mixed>
     */
    private function parseLlmResponse(array $responseData): array
    {
        $text = $responseData['content'][0]['text'] ?? '';

        // Strip markdown code fences if present
        $text = preg_replace('/^```(?:json)?\s*/m', '', $text);
        $text = preg_replace('/\s*```$/m', '', $text);

        $parsed = json_decode(trim($text), true);

        if (! is_array($parsed)) {
            throw new HttpException(422, "Couldn't extract a recipe — try manual entry?");
        }

        return $parsed;
    }

    /**
     * Validate that minimum required fields are present.
     *
     * @param  array<string, mixed>  $data
     */
    private function validateExtracted(array $data): void
    {
        if (empty($data['title'])) {
            throw new HttpException(422, "Couldn't extract a recipe — try manual entry?");
        }

        $instructions = $data['instructions'] ?? [];
        if (empty($instructions) || ! is_array($instructions)) {
            throw new HttpException(422, "Couldn't extract a recipe — try manual entry?");
        }
    }

    /**
     * Persist the imported recipe via RecipeService.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function persistImport(array $data, Family $family, User $user): array
    {
        $recipeData = [
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'servings' => isset($data['servings']) ? (int) $data['servings'] : null,
            'prep_time_minutes' => isset($data['prep_time']) ? (int) $data['prep_time'] : null,
            'cook_time_minutes' => isset($data['cook_time']) ? (int) $data['cook_time'] : null,
            'source_url' => $data['source_url'] ?? null,
            'source_type' => $data['source_type'] ?? 'url',
            'image_path' => $data['image_path'] ?? null,
            'instructions' => $data['instructions'] ?? [],
            'ingredients' => $data['ingredients'] ?? [],
        ];

        $recipe = $this->recipeService->createRecipe($family, $user, $recipeData);

        return ['recipe' => $recipe];
    }

    /**
     * Store uploaded recipe photo to the recipes disk.
     */
    private function storeRecipeImage(UploadedFile $photo): string
    {
        $filename = Str::uuid()->toString().'.'.$photo->guessExtension();

        return Storage::disk('public')->putFileAs('recipes', $photo, $filename);
    }

    /**
     * Strip navigation, footer, header, aside, and ad-related elements from HTML.
     */
    private function stripNonContentHtml(string $html): string
    {
        // Remove script and style blocks
        $html = preg_replace('/<script\b[^>]*>.*?<\/script>/si', '', $html) ?? $html;
        $html = preg_replace('/<style\b[^>]*>.*?<\/style>/si', '', $html) ?? $html;

        // Remove structural noise elements
        foreach (['nav', 'footer', 'aside', 'header'] as $tag) {
            $html = preg_replace("/<{$tag}\b[^>]*>.*?<\/{$tag}>/si", '', $html) ?? $html;
        }

        // Strip remaining tags and decode entities
        $text = strip_tags($html);
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        // Collapse whitespace
        $text = preg_replace('/\s{3,}/', "\n\n", $text) ?? $text;

        return trim($text);
    }

    /**
     * Resolve the Anthropic API key (platform key → family BYOK).
     */
    private function resolveApiKey(Family $family): string
    {
        // Platform key first
        $platformKey = config('kinhold.chatbot.api_key');
        if (! empty($platformKey)) {
            return $platformKey;
        }

        // BYOK fallback
        $settings = $family->settings ?? [];
        $aiMode = $settings['ai_mode'] ?? 'kinhold';

        if ($aiMode === 'byok' && ! empty($settings['ai_api_key'])) {
            $providerSlug = $settings['ai_provider'] ?? 'anthropic';

            if ($providerSlug === 'anthropic') {
                try {
                    return decrypt($settings['ai_api_key']);
                } catch (\Throwable $e) {
                    Log::warning('RecipeImportService: Failed to decrypt family AI API key');
                }
            }
        }

        throw new HttpException(
            500,
            'No AI API key configured. Ask a parent to add one in Settings > API Configuration.'
        );
    }

    /**
     * Resolve the model to use for extraction.
     */
    private function resolveModel(Family $family): string
    {
        $settings = $family->settings ?? [];

        return $settings['ai_model'] ?? config('kinhold.chatbot.model', 'claude-sonnet-4-5-20250929');
    }

    /**
     * Check if an IP address is public (not private, not reserved, not loopback).
     */
    private function isPublicIp(string $ip): bool
    {
        return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false;
    }
}
