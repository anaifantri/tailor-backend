<?php

namespace App\Services;

use App\Repositories\MaterialRepository;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;

class MaterialService
{
    private $materialRepository;

    public function __construct(MaterialRepository $materialRepository)
    {
        $this->materialRepository = $materialRepository;
    }

    public function getAll(?string $search = null, array $fields)
    {
        return $this->materialRepository->getAll($search, $fields);
    }

    public function getByHashedId(string $hashedId, array $fields)
    {
        try {
            $decryptedId = Crypt::decryptString($hashedId);
            return $this->materialRepository->getById((int) $decryptedId, $fields ?? ['*']);
        } catch (DecryptException $e) {
            throw new \InvalidArgumentException("ID tidak valid.");
        }
    }

    public function create(array $data)
    {
        if (isset($data['photo']) && $data['photo'] instanceof UploadedFile){
            $data['photo'] = $this->uploadPhoto($data['photo']);
        }
        return $this->materialRepository->create($data);
    }

    public function update(int $id, array $data)
    {
        $fields = ['id', 'photo'];
        $material = $this->materialRepository->getById($id, $fields);

        if (isset($data['photo']) && $data['photo'] instanceof UploadedFile){
            if(!empty($material->photo)){
                $this->deletePhoto($material->photo);
            }

            $data['photo'] = $this->uploadPhoto($data['photo']);
        }

        return $this->materialRepository->update($id, $data);
    }

    public function delete(string $hashedId)
    {
        $fields = ['id', 'photo'];
        $decryptedId = Crypt::decryptString($hashedId);
        $material = $this->materialRepository->getById((int) $decryptedId, $fields);

        if ($material->photo){
            $this->deletePhoto($material->photo);
        }

        return $this->materialRepository->delete((int) $decryptedId);
    }

    private function uploadPhoto(UploadedFile $photo)
    {
        return $photo->store('material-photo', 'public');
    }

    private function deletePhoto(string $photoPath)
    {
        $relativePath = 'material-photo/'. basename($photoPath);
        if(Storage::disk('public')->exists($relativePath)){
            Storage::disk('public')->delete($relativePath);
        }
    }
}