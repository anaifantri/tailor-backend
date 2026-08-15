<?php

namespace App\Services;

use App\Repositories\TailorRepository;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;

class TailorService
{
    private $tailorRepository;

    public function __construct(TailorRepository $tailorRepository)
    {
        $this->tailorRepository = $tailorRepository;
    }

    public function getAll(array $fields)
    {
        return $this->tailorRepository->getAll($fields);
    }

    public function getByHashedId(string $hashedId, array $fields)
    {
        try {
            $decryptedId = Crypt::decryptString($hashedId);
            return $this->tailorRepository->getById((int) $decryptedId, $fields ?? ['*']);
        } catch (DecryptException $e) {
            throw new \InvalidArgumentException("ID tidak valid.");
        }
    }

    public function create(array $data)
    {
        $fields = ['id', 'code'];
        $lastTailor = $this->tailorRepository->getLatestByCode();
        if(!$lastTailor){
            $number = 1;
        }else{
            $number = (int) substr($lastTailor->code, 4) + 1;
        }
        $newTailorCode = 'TLR-' . str_pad($number, 3, '0', STR_PAD_LEFT);

        $data['code'] = $newTailorCode;

        if (isset($data['photo']) && $data['photo'] instanceof UploadedFile){
            $data['photo'] = $this->uploadPhoto($data['photo']);
        }
        return $this->tailorRepository->create($data);
    }

    public function update(int $id, array $data)
    {
        $fields = ['id', 'photo'];
        $tailor = $this->tailorRepository->getById($id, $fields);

        if (isset($data['photo']) && $data['photo'] instanceof UploadedFile){
            if(!empty($tailor->photo)){
                $this->deletePhoto($tailor->photo);
            }

            $data['photo'] = $this->uploadPhoto($data['photo']);
        }

        return $this->tailorRepository->update($id, $data);
    }

    public function delete(string $hashedId)
    {
        $fields = ['id', 'photo'];
        $decryptedId = Crypt::decryptString($hashedId);
        $tailor = $this->tailorRepository->getById((int) $decryptedId, $fields);

        if ($tailor->photo){
            $this->deletePhoto($tailor->photo);
        }

        return $this->tailorRepository->delete((int) $decryptedId);
    }

    private function uploadPhoto(UploadedFile $photo)
    {
        return $photo->store('tailor-photo', 'public');
    }

    private function deletePhoto(string $photoPath)
    {
        $relativePath = 'tailor-photo/'. basename($photoPath);
        if(Storage::disk('public')->exists($relativePath)){
            Storage::disk('public')->delete($relativePath);
        }
    }
}