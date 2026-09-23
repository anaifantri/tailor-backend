<?php

namespace App\Services;

use App\Models\User;
use App\Notifications\PasswordChangedNotification;
use App\Repositories\UserRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class UserService
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getAll(int $perPage = 10, ?string $search = null, array $fields = ['*'])
    {
        return $this->userRepository->getAll($perPage, $search, $fields);
    }

    public function getByUlid(string $ulid, array $fields = ['*'])
    {
        return $this->userRepository->getByUlid($ulid, $fields);
    }

    public function findByUlid(string $ulid)
    {
        return $this->userRepository->findByUlid($ulid);
    }

    public function create(array $data)
    {
        if (isset($data['photo']) && $data['photo'] instanceof UploadedFile) {
            $data['photo'] = $this->uploadPhoto($data['photo']);
        }

        return $this->userRepository->create($data);
    }

    public function update(string $ulid, array $data)
    {
        $user = $this->userRepository->getByUlid($ulid, ['id', 'ulid', 'photo']);

        if (isset($data['photo']) && $data['photo'] instanceof UploadedFile) {
            if (!empty($user->photo)) {
                $this->deletePhoto($user->photo);
            }

            $data['photo'] = $this->uploadPhoto($data['photo']);
        }

        return $this->userRepository->update($ulid, $data);
    }

    public function delete(string $ulid)
    {
        $user = $this->userRepository->getByUlid($ulid, ['id', 'ulid', 'photo']);

        if ($user->photo) {
            $this->deletePhoto($user->photo);
        }

        return $this->userRepository->delete($ulid);
    }

    public function changePassword(User $user, array $data): void
    {
        if (!Hash::check($data['current_password'], $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['Password lama yang Anda masukkan salah.']
            ]);
        }

        $this->userRepository->updatePassword($user, $data['password']);
        
        $user->notify(new PasswordChangedNotification());
    }

    private function uploadPhoto(UploadedFile $photo): string
    {
        return $photo->store('user-photo', 'public');
    }

    private function deletePhoto(string $photoPath): void
    {
        $relativePath = 'user-photo/' . basename($photoPath);
        if (Storage::disk('public')->exists($relativePath)) {
            Storage::disk('public')->delete($relativePath);
        }
    }
}