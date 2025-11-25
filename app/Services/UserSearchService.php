<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class UserSearchService
{
    /**
     * Perform a paginated search for users with optimized select statements.
     */
    public function paginated(?string $query, int $perPage = 15, bool $withQueryString = true): LengthAwarePaginator
    {
        $builder = $this->baseQuery($query)
            ->orderByDesc('users.id');

        $paginator = $builder->paginate($perPage);

        return $withQueryString ? $paginator->withQueryString() : $paginator;
    }

    /**
     * Search users for API responses (non paginated, limited results).
     */
    public function searchCollection(?string $query, int $limit = 20): Collection
    {
        return $this->baseQuery($query)
            ->limit($limit)
            ->get();
    }

    /**
     * Base query builder shared between paginated and collection searches.
     */
    protected function baseQuery(?string $query)
    {
        return User::query()
            ->select(['users.id', 'users.first_name', 'users.last_name', 'users.email', 'users.created_at'])
            ->with(['address:id,user_id,country,city,post_code,street'])
            ->when($query, function ($q) use ($query) {
                $q->where(function ($sub) use ($query) {
                    $likeQuery = '%' . $query . '%';
                    $sub->where('users.first_name', 'like', $likeQuery)
                        ->orWhere('users.last_name', 'like', $likeQuery)
                        ->orWhere('users.email', 'like', $likeQuery)
                        ->orWhereHas('address', function ($address) use ($likeQuery) {
                            $address->where('country', 'like', $likeQuery)
                                ->orWhere('city', 'like', $likeQuery)
                                ->orWhere('post_code', 'like', $likeQuery)
                                ->orWhere('street', 'like', $likeQuery);
                        });
                });
            });
    }
}

