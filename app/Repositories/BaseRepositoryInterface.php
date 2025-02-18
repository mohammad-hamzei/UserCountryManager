<?php

namespace App\Repositories;

interface BaseRepositoryInterface
{
    public function getAll(array $filters = []): array;
    public function getById($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
}

