<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserRepository 
{
    public function getAll(int $perPage = 10, ?string $search = null, array $fields = ['*'])
    {
        return User::select($fields)->search($search)->latest()->paginate($perPage);
    }

    public function getByUlid(string $ulid, array $fields = ['*'])
    {
        return User::select($fields)->where('ulid', $ulid)->firstOrFail();
    }

    public function findByUlid(string $ulid)
    {
        return User::where('ulid', $ulid)->firstOrFail();
    }
    
    public function updatePassword(User $user, string $newPassword): bool
    {
        return $user->update([
            'password' => Hash::make($newPassword)
        ]);
    }

    public function create(array $data)
    {
        return User::create($data);
    }

    public function update(string $ulid, array $data)
    {
        $user = $this->findByUlid($ulid);
        $user->update($data);

        return $user;
    }

    public function delete(string $ulid)
    {
        $user = $this->findByUlid($ulid);
        $user->delete();
        $user->tokens()->delete();
    }
}