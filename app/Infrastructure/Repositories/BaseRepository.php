<?php

namespace App\Infrastructure\Repositories;

use Illuminate\Database\Eloquent\Model;

/**
 * Spolecny zaklad. Konkretni repozitar doplni jen to, co entita potrebuje navic.
 *
 * @template TModel of Model
 */
abstract class BaseRepository implements RepositoryInterface
{
    protected Model $model;

    public function __construct()
    {
        $class = $this->modelClass();
        $this->model = new $class;
    }

    abstract protected function modelClass(): string;

    public function all()
    {
        return $this->model->newQuery()->latest()->get();
    }

    public function find(int|string $id)
    {
        return $this->model->newQuery()->find($id);
    }

    public function findOrFail(int|string $id)
    {
        return $this->model->newQuery()->findOrFail($id);
    }

    public function paginate(int $perPage = 15)
    {
        return $this->model->newQuery()->latest()->paginate($perPage);
    }

    public function create(array $data)
    {
        return $this->model->newQuery()->create($data);
    }

    public function update(int|string $id, array $data)
    {
        $item = $this->findOrFail($id);
        $item->update($data);

        return $item->fresh();
    }

    public function delete(int|string $id)
    {
        $item = $this->findOrFail($id);

        return (bool) $item->delete();
    }
}
