<?php

namespace App\Services;

use App\Models\ShortenedUrl;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class UrlShortenerService
{
    /**
     * Cache TTL for short codes (7 days in seconds).
     */
    private const CACHE_TTL = 604800;

    /**
     * Length of the generated short code.
     */
    private const SHORT_CODE_LENGTH = 6;

    /**
     * Generate a unique short code for a URL.
     */
    public function generateShortCode(): string
    {
        do {
            $shortCode = Str::random(self::SHORT_CODE_LENGTH);
        } while ($this->shortCodeExists($shortCode));

        return $shortCode;
    }

    /**
     * Check if a short code already exists in database or cache.
     */
    private function shortCodeExists(string $shortCode): bool
    {
        // Check cache first for performance
        if (Cache::has($this->getCacheKey($shortCode))) {
            return true;
        }

        // Check database
        return ShortenedUrl::query()->where('short_code', $shortCode)->exists();
    }

    /**
     * Shorten a URL for a given user.
     */
    public function shortenUrl(string $originalUrl, User $user): ShortenedUrl
    {
        $shortCode = $this->generateShortCode();

        $shortenedUrl = ShortenedUrl::query()->create([
            'user_id' => $user->id,
            'original_url' => $originalUrl,
            'short_code' => $shortCode,
            'clicks' => 0,
        ]);

        // Cache the shortened URL for fast lookups
        $this->cacheUrl($shortenedUrl);

        return $shortenedUrl;
    }

    /**
     * Get a shortened URL by its short code (with caching).
     */
    public function getUrlByCode(string $shortCode): ?ShortenedUrl
    {
        $cacheKey = $this->getCacheKey($shortCode);

        // Try to get from cache first
        $cachedUrlId = Cache::get($cacheKey);

        if ($cachedUrlId) {
            return ShortenedUrl::query()->find($cachedUrlId);
        }

        // If not in cache, get from database and cache it
        $shortenedUrl = ShortenedUrl::query()
            ->where('short_code', $shortCode)
            ->first();

        if ($shortenedUrl) {
            $this->cacheUrl($shortenedUrl);
        }

        return $shortenedUrl;
    }

    /**
     * Cache a shortened URL.
     */
    private function cacheUrl(ShortenedUrl $shortenedUrl): void
    {
        $cacheKey = $this->getCacheKey($shortenedUrl->short_code);
        Cache::put($cacheKey, $shortenedUrl->id, self::CACHE_TTL);
    }

    /**
     * Remove a shortened URL from cache.
     */
    public function removeCachedUrl(string $shortCode): void
    {
        Cache::forget($this->getCacheKey($shortCode));
    }

    /**
     * Get the cache key for a short code.
     */
    private function getCacheKey(string $shortCode): string
    {
        return "url_shortener:{$shortCode}";
    }
}
