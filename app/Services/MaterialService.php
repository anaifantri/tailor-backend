<?php

namespace App\Services;

use App\Models\Material;
use App\Repositories\MaterialRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class MaterialService
{
    private MaterialRepository $materialRepository;

    public function __construct(MaterialRepository $materialRepository)
    {
        $this->materialRepository = $materialRepository;
    }

    public function getAll(int $perPage = 10, ?string $search = null, array $fields = ['*']): LengthAwarePaginator
    {
        return $this->materialRepository->getAll($perPage, $search, $fields);
    }

    public function getByUlid(string $ulid, array $fields = ['*']): Material
    {
        return $this->materialRepository->getByUlid($ulid, $fields);
    }

    public function findByUlid(string $ulid): Material
    {
        return $this->materialRepository->findByUlid($ulid);
    }

    public function create(array $data): Material
    {
        if (isset($data['photo']) && $data['photo'] instanceof UploadedFile) {
            $data['photo'] = $this->uploadPhoto($data['photo']);
        }

        return $this->materialRepository->create($data);
    }

    public function update(string $ulid, array $data): Material
    {
        $material = $this->materialRepository->findByUlid($ulid);

        if (isset($data['photo']) && $data['photo'] instanceof UploadedFile) {
            if ($material->getRawOriginal('photo')) {
                $this->deletePhoto($material->getRawOriginal('photo'));
            }

            $data['photo'] = $this->uploadPhoto($data['photo']);
        }

        return $this->materialRepository->update($ulid, $data);
    }

    public function delete(string $ulid): void
    {
        $material = $this->materialRepository->findByUlid($ulid);

        if ($material->getRawOriginal('photo')) {
            $this->deletePhoto($material->getRawOriginal('photo'));
        }

        $this->materialRepository->delete($ulid);
    }

    private function uploadPhoto(UploadedFile $photo): string
    {
        return $photo->store('material-photo', 'public');
    }

    private function deletePhoto(string $photoPath): void
    {
        $relativePath = 'material-photo/' . basename($photoPath);
        if (Storage::disk('public')->exists($relativePath)) {
            Storage::disk('public')->delete($relativePath);
        }
    }
}