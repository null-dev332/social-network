<?php

namespace App\Infrastructure\Repositories;

interface RepositoryInterface
{
    public function all();

    public function find(int|string $id);

    public function findOrFail(int|string $id);

    public function paginate(int $perPage = 15);

    public function create(array $data);

    public function update(int|string $id, array $data);

    public function delete(int|string $id);
}
