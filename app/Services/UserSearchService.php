<?php
namespace App\Services;

use App\Helpers\CacheHelper;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class UserSearchService
{
    /**
     * Cache TTL in seconds (5 minutes)
     */
    private const CACHE_TTL = 300;

    /**
     * Get paginated search results with caching.
     *
     * @param string|null $query
     * @param int $perPage
     * @param int $page
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginated(?string $query, int $perPage = 20, int $page = 1)
    {
        try {
            $query = trim((string)$query);
            $cacheKey = $this->getCacheKey($query, $perPage, $page);

            return CacheHelper::rememberWithTags(
                ['users', 'user-search'],
                $cacheKey,
                self::CACHE_TTL,
                function () use ($query, $perPage, $page) {
                    return $this->performSearch($query, $perPage, $page);
                }
            );
        } catch (\Exception $e) {
            Log::error('User search failed', [
                'query' => $query,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Fallback to non-cached search on error
            return $this->performSearch($query, $perPage, $page);
        }
    }

    /**
     * Perform the actual search query.
     *
     * @param string $query
     * @param int $perPage
     * @param int $page
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    private function performSearch(string $query, int $perPage, int $page)
    {
        $builder = User::query()
            ->select('id', 'first_name', 'last_name', 'email');

        if ($query !== '') {
            if (strpos($query, '@') !== false) {
                $builder->where('email', $query);
            } else {
                $builder->whereRaw("MATCH(first_name, last_name, email) AGAINST(? IN BOOLEAN MODE)", [$query]);
            }
        }

        return $builder->orderByDesc('id')->paginate($perPage, ['*'], 'page', $page);
    }

    /**
     * Generate cache key for search query.
     *
     * @param string $query
     * @param int $perPage
     * @param int $page
     * @return string
     */
    private function getCacheKey(string $query, int $perPage, int $page): string
    {
        return 'user_search:' . md5($query . '|' . $perPage . '|' . $page);
    }

    /**
     * Clear search cache.
     *
     * @return void
     */
    public function clearCache(): void
    {
        CacheHelper::flushTags(['users', 'user-search']);
    }
}