<?php

namespace App\Services;

use App\Models\Family;
use App\Models\FamilyRestaurant;
use App\Models\Restaurant;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

class RestaurantImportService
{
    /**
     * Import a restaurant from a Google Maps URL.
     *
     * Attempts to extract restaurant info from the URL. Falls back to
     * creating a basic entry with just the URL if extraction fails.
     */
    public function importFromUrl(string $url, Family $family): Restaurant
    {
        $data = $this->extractFromUrl($url);

        // Find or create the global restaurant record
        $restaurant = Restaurant::firstOrCreate(
            ['google_maps_url' => $url],
            [
                'name' => $data['name'] ?? $this->extractNameFromUrl($url),
                'cuisine' => $data['cuisine'] ?? null,
                'address' => $data['address'] ?? null,
                'phone' => $data['phone'] ?? null,
            ]
        );

        // Link to family if not already linked
        FamilyRestaurant::firstOrCreate(
            ['family_id' => $family->id, 'restaurant_id' => $restaurant->id]
        );

        return $restaurant;
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
     * Try to extract restaurant data from a Google Maps URL.
     * This is a best-effort parser — Google Maps URLs vary in format.
     */
    private function extractFromUrl(string $url): array
    {
        $data = [];

        try {
            // Google Maps URLs often contain the place name in the path
            // Format: https://maps.google.com/maps/place/Restaurant+Name/...
            // Format: https://goo.gl/maps/xxx (short URL)
            if (preg_match('#/place/([^/@]+)#', $url, $matches)) {
                $data['name'] = urldecode(str_replace('+', ' ', $matches[1]));
            }

            // Try to extract address from URL parameters
            if (preg_match('#/place/[^/]+/(@[\d.-]+,[\d.-]+)#', $url, $matches)) {
                // Has coordinates but not a parseable address from URL alone
            }
        } catch (\Throwable $e) {
            Log::warning('Restaurant URL parsing failed', ['url' => $url, 'error' => $e->getMessage()]);
        }

        return $data;
    }

    /**
     * Extract a reasonable name from a URL as fallback.
     */
    private function extractNameFromUrl(string $url): string
    {
        // Try to get place name from Google Maps URL
        if (preg_match('#/place/([^/@]+)#', $url, $matches)) {
            return urldecode(str_replace('+', ' ', $matches[1]));
        }

        // Fallback: use domain or generic name
        $parsed = parse_url($url);

        return $parsed['host'] ?? 'Imported Restaurant';
    }
}
