<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserRepository 
{
    public function getAll(int $perPage = 10, ?string $search = null, array $fields){
        return User::select($fields)->search($search)->latest()->paginate($perPage);
    }

    public function getById(int $id, array $fields){
        return User::select($fields)->findOrFail($id);
    }

    public function findById(int $id){
        return User::findOrFail($id);
    }
    
    public function updatePassword(User $user, string $newPassword): bool
    {
        return $user->update([
            'password' => Hash::make($newPassword)
        ]);
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