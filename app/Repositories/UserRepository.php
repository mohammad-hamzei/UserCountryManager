<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    public function getAllUsers(array $filters = [], $sortBy = 'name', $sortOrder = 'asc')
    {
        $query = User::query()
            ->leftJoin('countries', 'users.country_id', '=', 'countries.id')
            ->leftJoin('currencies', 'users.currency_id', '=', 'currencies.id')
            ->select('users.*', 'countries.name as country_name', 'currencies.code as currency_code');

        if (!empty($filters['country'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('countries.code', $filters['country'])
                    ->orWhere('countries.name', 'like', '%' . $filters['country'] . '%');
            });
        }

        if (!empty($filters['currency'])) {
            $query->where('currencies.code', $filters['currency']);
        }

        if ($sortBy === 'country') {
            $query->orderBy('countries.name', $sortOrder);
        } elseif ($sortBy === 'currency') {
            $query->orderBy('currencies.code', $sortOrder);
        } else {
            $query->orderBy("users.$sortBy", $sortOrder);
        }

        return $query->get();
    }
}
