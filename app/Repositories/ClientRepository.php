<?php

namespace App\Repositories;

use App\Models\Client;

class ClientRepository
{
    public function getAll(?string $search = null, array $fields){
        return Client::select($fields)->search($search)->latest()->paginate(10);
    }

    public function getLatestByCode()
    {
        return Client::orderBy('code', 'desc')->latest()->first();
    }

    public function getById(int $id, array $fields){
        return Client::select($fields)->findOrFail($id);
    }

    public function create(array $data){
        return Client::create($data);
    }

    public function update(int $id, array $data){
        $client = Client::findOrFail($id);

        $client->update($data);

        return $client;
    }

    public function delete(int $id){
        $client = Client::findOrFail($id);

        $client->delete();
    }
}