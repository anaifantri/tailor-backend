<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository 
{
    public function getAll(int $perPage = 10, ?string $search = null, array $fields){
        return User::select($fields)->search($search)->latest()->paginate($perPage);
    }

    public function getById(int $id, array $fields){
        return User::select($fields)->findOrFail($id);
    }

    public function create(array $data){
        return User::create($data);
    }

    public function update(int $id, array $data){
        $user = User::findOrFail($id);

        $user->update($data);

        return $user;
    }

    public function delete(int $id){
        $user = User::findOrFail($id);

        $user->delete();
        $user->tokens()->delete();
    }
}