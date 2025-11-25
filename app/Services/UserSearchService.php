<?php
namespace App\Services;

use App\Models\User;

class UserSearchService
{
    public function paginated(?string $query, int $perPage = 20)
    {
        $builder = User::query()
            ->select('id','first_name','last_name','email'); // light fields only

        if ($query) {
            // Make sure FULLTEXT index exists on these columns before using MATCH
            $builder->whereRaw(
                "MATCH(first_name, last_name, email) AGAINST(? IN BOOLEAN MODE)",
                [$query]
            );
        }

        return $builder->orderByDesc('id')->paginate($perPage);
    }

    public function searchCollection(?string $query, int $limit = 20)
    {
        $builder = User::query()
            ->select('id','first_name','last_name','email');

        if ($query) {
            $builder->whereRaw(
                "MATCH(first_name, last_name, email) AGAINST(? IN BOOLEAN MODE)",
                [$query]
            );
        }

        return $builder->limit($limit)->get();
    }
}