<?php
namespace App\Services;

use App\Models\User;

class UserSearchService
{
    public function paginated(?string $query, int $perPage = 20, int $page = 1)
    {
        $builder = User::query()
            ->select('id','first_name','last_name','email');

        $query = trim((string)$query);

        if ($query !== '') {
            if (strpos($query, '@') !== false) {
                $builder->where('email', $query);
            } else {
                $builder->whereRaw("MATCH(first_name, last_name, email) AGAINST(? IN BOOLEAN MODE)", [$query]);
            }
        }
        
        return $builder->orderByDesc('id')->paginate($perPage, ['*'], 'page', $page);
    }
}