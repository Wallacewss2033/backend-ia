<?php

namespace App\Services;

use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class UserService
{
    protected UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getAllUsers(): Collection
    {
        return $this->userRepository->all();
    }

    public function getUserById(int $id): ?Model
    {
        return $this->userRepository->find($id);
    }

    public function createUser(array $data): Model
    {
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }
        return $this->userRepository->create($data);
    }

    public function updateUser(int $id, array $data): bool
    {
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }
        return $this->userRepository->update($id, $data);
    }

    public function deleteUser(int $id): bool
    {
        return $this->userRepository->delete($id);
    }

    public function getUserByEmail(string $email): ?Model
    {
        return $this->userRepository->findByEmail($email);
    }

    public function getUserByField(string $field, mixed $value): ?Model
    {
        return $this->userRepository->findByField($field, $value);
    }

    public function countUsers(): int
    {
        return $this->userRepository->count();
    }
}
