<?php

namespace App\Infra\Memory;

use App\Domain\User\User;
use App\Domain\User\UserPersistenceInterface;

class UserMemory implements UserPersistenceInterface
{
    public function create(User $user): void
    {

    }

    public function isCpfAlreadyCreated(User $user): bool
    {
        return false;
    }

    public function isEmailAlreadyCreated(User $user): bool
    {
        return false;
    }

    public function findAll(User $user): array
    {
        return [];
    }

    public function isExistentId(User $user): bool
    {
        return true;
    }

    public function editName(User $user): void
    {

    }

    public function editCpf(User $user): void
    {

    }

    public function editEmail(User $user): void
    {

    }
}
