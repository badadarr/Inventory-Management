<?php

namespace App\Repositories;

use App\Models\Sales;


class SalesRepository
{
    public function all()
    {
        return Sales::orderBy('name')->paginate(10);
    }

    public function find(int $id): ?Sales
    {
        return Sales::find($id);
    }

    public function create(array $data): Sales
    {
        return Sales::create($data);
    }

    public function update(Sales $sales, array $data): bool
    {
        return $sales->update($data);
    }

    public function delete(Sales $sales): bool
    {
        return $sales->delete();
    }
}
