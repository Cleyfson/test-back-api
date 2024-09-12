<?php

namespace App\Infra\Memory;

use App\Domain\User\User;
use App\Domain\User\UserPersistenceInterface;

class UserMemory implements UserPersistenceInterface
{
    private array $users = [];

    public function create(User $user): void
    {
        $this->users[$user->getId()] = $user;
    }

    public function isCpfAlreadyCreated(User $user): bool
    {
        foreach ($this->users as $existingUser) {
            if ($existingUser->getCpf() === $user->getCpf()) {
                return true;
            }
        }
        return false;
    }

    public function isEmailAlreadyCreated(User $user): bool
    {
        foreach ($this->users as $existingUser) {
            if ($existingUser->getEmail() === $user->getEmail()) {
                return true;
            }
        }
        return false;
    }

    public function findAll(User $user): array
    {
        return array_values($this->users);
    }

    public function isExistentId(User $user): bool
    {
        return isset($this->users[$user->getId()]);
    }

    public function editName(User $user): void
    {
        if (isset($this->users[$user->getId()])) {
            $this->users[$user->getId()]->setName($user->getName());
        }
    }

    public function editCpf(User $user): void
    {
        if (isset($this->users[$user->getId()])) {
            $this->users[$user->getId()]->setCpf($user->getCpf());
        }
    }

    public function editEmail(User $user): void
    {
        if (isset($this->users[$user->getId()])) {
            $this->users[$user->getId()]->setEmail($user->getEmail());
        }
    }
}
