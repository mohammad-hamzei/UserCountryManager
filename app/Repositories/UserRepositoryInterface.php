<?php

namespace App\Repositories;

interface UserRepositoryInterface extends BaseRepositoryInterface
{
    public function getAllUsers(array $filters = [], $sortBy = 'name', $sortOrder = 'asc');

}
