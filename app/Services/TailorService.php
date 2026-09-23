<?php

namespace App\Services;

use App\Models\Tailor;
use App\Repositories\TailorRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class TailorService
{
    private TailorRepository $tailorRepository;

    public function __construct(TailorRepository $tailorRepository)
    {
        $this->tailorRepository = $tailorRepository;
    }

    public function getAll(int $perPage = 10, ?string $search = null, array $fields = ['*']): LengthAwarePaginator
    {
        return $this->tailorRepository->getAll($perPage, $search, $fields);
    }

    public function getByUlid(string $ulid, array $fields = ['*']): Tailor
    {
        return $this->tailorRepository->getByUlid($ulid, $fields);
    }

    public function findByUlid(string $ulid): Tailor
    {
        return $this->tailorRepository->findByUlid($ulid);
    }

    public function create(array $data): Tailor
    {
        $lastTailor = $this->tailorRepository->getLatestByCode();
        if (!$lastTailor) {
            $number = 1;
        } else {
            $number = (int) substr($lastTailor->code, 4) + 1;
        }
        
        $data['code'] = 'TLR-' . str_pad((string) $number, 3, '0', STR_PAD_LEFT);

        if (isset($data['photo']) && $data['photo'] instanceof UploadedFile) {
            $data['photo'] = $this->uploadPhoto($data['photo']);
        }

        return $this->tailorRepository->create($data);
    }

    public function update(string $ulid, array $data): Tailor
    {
        $tailor = $this->tailorRepository->findByUlid($ulid);

        if (isset($data['photo']) && $data['photo'] instanceof UploadedFile) {
            if ($tailor->getRawOriginal('photo')) {
                $this->deletePhoto($tailor->getRawOriginal('photo'));
            }

            $data['photo'] = $this->uploadPhoto($data['photo']);
        }

        return $this->tailorRepository->update($ulid, $data);
    }

    public function delete(string $ulid): void
    {
        $tailor = $this->tailorRepository->findByUlid($ulid);

        if ($tailor->getRawOriginal('photo')) {
            $this->deletePhoto($tailor->getRawOriginal('photo'));
        }

        $this->tailorRepository->delete($ulid);
    }

    private function uploadPhoto(UploadedFile $photo): string
    {
        return $photo->store('tailor-photo', 'public');
    }

    private function deletePhoto(string $photoPath): void
    {
        $relativePath = 'tailor-photo/' . basename($photoPath);
        if (Storage::disk('public')->exists($relativePath)) {
            Storage::disk('public')->delete($relativePath);
        }
    }
}