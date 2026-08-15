<?php

namespace App\Repositories;

use App\Models\Tailor;

class TailorRepository
{
    public function getAll(array $fields){
        return Tailor::select($fields)->latest()->paginate(10);
    }

    public function getLatestByCode()
    {
        return Tailor::orderBy('code', 'desc')->latest()->first();
    }

    public function getById(int $id, array $fields){
        return Tailor::select($fields)->findOrFail($id);
    }

    public function create(array $data){
        return Tailor::create($data);
    }

    public function update(int $id, array $data){
        $tailor = Tailor::findOrFail($id);

        $tailor->update($data);

        return $tailor;
    }

    public function delete(int $id){
        $tailor = Tailor::findOrFail($id);

        $tailor->delete();
    }
}