<?php

namespace App\Services;

use App\Repositories\UserRepository;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;

class UserService
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getAll(array $fields)
    {
        return $this->userRepository->getAll($fields);
    }

    public function getByHashedId(string $hashedId, array $fields)
    {
        try {
            // Dekripsi string acak dari React kembali menjadi integer ID asli
            $decryptedId = Crypt::decryptString($hashedId);
            
            // Oper ID asli dan array $fields ke Repository
            return $this->userRepository->getById((int) $decryptedId, $fields);
            
        } catch (DecryptException $e) {
            throw new \InvalidArgumentException("ID tidak valid.");
        }
        // return $this->userRepository->getById($hasedId, $fields ?? ['*']);
    }

    public function create(array $data)
    {
        if (isset($data['photo']) && $data['photo'] instanceof UploadedFile){
            $data['photo'] = $this->uploadPhoto($data['photo']);
        }

        return $this->userRepository->create($data);
    }

    public function update(int $id, array $data)
    {
        $fields = ['id', 'photo'];
        $user = $this->userRepository->getById($id, $fields);

        if (isset($data['photo']) && $data['photo'] instanceof UploadedFile){
            if(!empty($user->photo)){
                $this->deletePhoto($user->photo);
            }

            $data['photo'] = $this->uploadPhoto($data['photo']);
        }

        return $this->userRepository->update($id, $data);
    }

    public function delete(string $hashedId)
    {
        $fields = ['id', 'photo'];
        $decryptedId = Crypt::decryptString($hashedId);
        $user = $this->userRepository->getById((int) $decryptedId, $fields);

        if ($user->photo){
            $this->deletePhoto($user->photo);
        }

        return $this->userRepository->delete((int) $decryptedId);
    }

    private function uploadPhoto(UploadedFile $photo)
    {
        return $photo->store('user-photo', 'public');
    }

    private function deletePhoto(string $photoPath)
    {
        $relativePath = 'user-photo/'. basename($photoPath);
        if(Storage::disk('public')->exists($relativePath)){
            Storage::disk('public')->delete($relativePath);
        }
    }
}