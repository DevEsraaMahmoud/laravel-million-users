<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CacheHelper
{
    /**
     * Check if the current cache driver supports tags.
     *
     * @return bool
     */
    public static function supportsTags(): bool
    {
        $driver = config('cache.default');
        return in_array($driver, ['redis', 'memcached']);
    }

    /**
     * Safely use cache tags, falling back to regular cache if tags aren't supported.
     *
     * @param array $tags
     * @param callable $callback
     * @return mixed
     */
    public static function withTags(array $tags, callable $callback)
    {
        if (self::supportsTags()) {
            try {
                return Cache::tags($tags)->getStore();
            } catch (\Exception $e) {
                Log::warning('Cache tags not supported, falling back to regular cache', [
                    'driver' => config('cache.default'),
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return Cache::getStore();
    }

    /**
     * Safely remember with tags, falling back to regular cache if tags aren't supported.
     *
     * @param array $tags
     * @param string $key
     * @param int $ttl
     * @param callable $callback
     * @return mixed
     */
    public static function rememberWithTags(array $tags, string $key, int $ttl, callable $callback)
    {
        if (self::supportsTags()) {
            try {
                return Cache::tags($tags)->remember($key, $ttl, $callback);
            } catch (\Exception $e) {
                Log::warning('Cache tags failed, using regular cache', [
                    'key' => $key,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return Cache::remember($key, $ttl, $callback);
    }

    /**
     * Safely flush cache by tags, falling back to clearing all cache if tags aren't supported.
     *
     * @param array $tags
     * @return void
     */
    public static function flushTags(array $tags): void
    {
        if (self::supportsTags()) {
            try {
                Cache::tags($tags)->flush();
                return;
            } catch (\Exception $e) {
                Log::warning('Cache tag flush failed', [
                    'tags' => $tags,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // Fallback: clear all cache if tags aren't supported
        // In production, you might want to be more selective
        try {
            Cache::flush();
        } catch (\Exception $e) {
            Log::warning('Cache flush failed', [
                'error' => $e->getMessage(),
            ]);
        }
    }
}

