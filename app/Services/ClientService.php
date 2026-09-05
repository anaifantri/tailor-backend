<?php

namespace App\Services;

use App\Repositories\ClientRepository;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;

class ClientService
{
    private $clientRepository;

    public function __construct(ClientRepository $clientRepository)
    {
        $this->clientRepository = $clientRepository;
    }

    public function getAll(?string $search = null, array $fields)
    {
        return $this->clientRepository->getAll($search, $fields);
    }

    public function getByHashedId(string $hashedId, array $fields)
    {
        try {
            $decryptedId = Crypt::decryptString($hashedId);
            return $this->clientRepository->getById((int) $decryptedId, $fields ?? ['*']);
        } catch (DecryptException $e) {
            throw new \InvalidArgumentException("ID tidak valid.");
        }
    }

    public function create(array $data)
    {
        $fields = ['id', 'code'];
        $lastClient = $this->clientRepository->getLatestByCode();
        if(!$lastClient){
            $number = 1;
        }else{
            $number = (int) substr($lastClient->code, 5) + 1;
        }
        $newClientCode = 'CUST-' . str_pad($number, 5, '0', STR_PAD_LEFT);

        $data['code'] = $newClientCode;

        return $this->clientRepository->create($data);
    }

    public function update(int $id, array $data)
    {
        $fields = ['id'];

        return $this->clientRepository->update($id, $data);
    }

    public function delete(string $hashedId)
    {
        $fields = ['id'];
        $decryptedId = Crypt::decryptString($hashedId);

        return $this->clientRepository->delete((int) $decryptedId);
    }
}