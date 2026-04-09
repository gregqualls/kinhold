<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class UpdateCheckService
{
    /**
     * Check if a newer version of Kinhold is available.
     *
     * Returns update info or null if up-to-date / check disabled / error.
     * Results are cached for 24 hours to avoid hammering GitHub.
     *
     * @return array{latest_version: string, url: string, published_at: string}|null
     */
    public function getStatus(): ?array
    {
        if (! config('version.update_check')) {
            return null;
        }

        try {
            return Cache::remember('kinhold:update_check', 86400, function () {
                return $this->checkGitHub();
            });
        } catch (\Throwable $e) {
            Log::warning('Update check failed', ['error' => $e->getMessage()]);

            return null;
        }
    }

    /**
     * Query GitHub Releases API for the latest release.
     */
    private function checkGitHub(): ?array
    {
        $repo = config('version.github_repo');
        $currentVersion = config('version.current');

        $response = Http::timeout(5)
            ->withHeaders(['Accept' => 'application/vnd.github.v3+json'])
            ->get("https://api.github.com/repos/{$repo}/releases/latest");

        if (! $response->successful()) {
            return null;
        }

        $data = $response->json();
        $latestVersion = ltrim($data['tag_name'] ?? '', 'v');

        if (empty($latestVersion)) {
            return null;
        }

        // Only notify if the remote version is actually newer
        if (version_compare($latestVersion, $currentVersion, '<=')) {
            return null;
        }

        return [
            'latest_version' => $latestVersion,
            'url' => $data['html_url'] ?? "https://github.com/{$repo}/releases",
            'published_at' => $data['published_at'] ?? null,
        ];
    }
}
