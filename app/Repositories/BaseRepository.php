<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

class BaseRepository implements BaseRepositoryInterface
{
    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function getAll(array $filters = []): array
    {
        $query = $this->model->query();

        foreach ($filters as $field => $value) {
            $query->where($field, $value);
        }

        return $query->get()->toArray();
    }

    public function getById($id)
    {
        return $this->model->findOrFail($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $record = $this->getById($id);
        $record->update($data);

        return $record;
    }

    public function delete($id)
    {
        $record = $this->getById($id);
        $record->delete();

        return true;
    }
}

