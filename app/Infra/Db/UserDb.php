<?php

namespace App\Infra\Db;

use App\Domain\User\User;
use App\Domain\User\UserDataValidator;
use App\Domain\User\UserPersistenceInterface;
use Illuminate\Support\Facades\DB;

class UserDb implements UserPersistenceInterface
{
    private const TABLE_NAME = 'user';

    private const COLUMN_UUID = 'uuid';
    private const COLUMN_NAME = 'name';
    private const COLUMN_EMAIL = 'email';
    private const COLUMN_CPF = 'cpf';
    private const COLUMN_CREATED_AT = 'created_at';
    private const COLUMN_DELETED_AT = 'deleted_at';
    private const COLUMN_UPDATED_AT = 'updated_at';
    private const MINIMUM_MONTHS_FOR_CREDIT_ELIGIBILITY = '6';

    public function create(User $user): void
    {
        DB::table(self::TABLE_NAME)->insert([
            self::COLUMN_UUID => $user->getId(),
            self::COLUMN_NAME => $user->getName(),
            self::COLUMN_EMAIL => $user->getEmail(),
            self::COLUMN_CPF => $user->getCpf(),
            self::COLUMN_CREATED_AT => $user->getDateCreation(),
        ]);
    }

    public function isCpfAlreadyCreated(User $user): bool
    {
        return DB::table(self::TABLE_NAME)
            ->where([self::COLUMN_CPF => $user->getCpf()])
            ->exists()
        ;
    }

    public function isEmailAlreadyCreated(User $user): bool
    {
        return DB::table(self::TABLE_NAME)
            ->where([self::COLUMN_EMAIL => $user->getEmail()])
            ->exists()
        ;
    }

    public function findAll(User $user): array
    {
        $users = [];

        $records = DB::table(self::TABLE_NAME)
            ->select([
                self::COLUMN_UUID,
                self::COLUMN_NAME,
                self::COLUMN_EMAIL,
                self::COLUMN_CPF,
            ])
            ->where([
                self::COLUMN_DELETED_AT => null
            ])
            ->get()
        ;

        foreach ($records as $record) {
            $users[] = (new User(new UserDb()))
                ->setDataValidator(new UserDataValidator())
                ->setId($record->uuid)
                ->setName($record->name)
                ->setCpf($record->cpf)
                ->setEmail($record->email)
            ;
        }

        return $users;
    }

    public function isExistentId(User $user): bool
    {
        return DB::table(self::TABLE_NAME)
            ->where([self::COLUMN_UUID => $user->getId()])
            ->exists()
        ;
    }

    public function findById(string $id): User
    {
        $requiredMonthsForEligibility = self::MINIMUM_MONTHS_FOR_CREDIT_ELIGIBILITY;

        $record = DB::table(self::TABLE_NAME)
            ->select([
                self::COLUMN_UUID . ' as uuid',
                self::COLUMN_NAME . ' as name',
                self::COLUMN_EMAIL . ' as email',
                self::COLUMN_CPF . ' as cpf',
                self::COLUMN_CREATED_AT . ' as admission_date',
                DB::raw("
                    CASE
                        WHEN datetime(" . self::COLUMN_CREATED_AT . ") <= datetime('now', '-' || $requiredMonthsForEligibility || ' month') THEN 1
                        ELSE 0
                    END as is_credit_eligible
                ")
            ])
            ->where(self::COLUMN_UUID, $id)
            ->whereNull(self::COLUMN_DELETED_AT)
            ->first()
        ;

        $user = (new User(new UserDb()))
            ->setDataValidator(new UserDataValidator())
            ->setId($record->uuid)
            ->setName($record->name)
            ->setCpf($record->cpf)
            ->setEmail($record->email)
            ->setIsCreditEligible($record->is_credit_eligible)
        ;

        return $user;
    }

    public function editName(User $user): void
    {
        DB::table(self::TABLE_NAME)
            ->where([self::COLUMN_UUID => $user->getId()])
            ->update([
                self::COLUMN_NAME => $user->getName(),
                self::COLUMN_UPDATED_AT => $user->getDateEdition(),
            ])
        ;
    }

    public function editCpf(User $user): void
    {
        DB::table(self::TABLE_NAME)
            ->where([self::COLUMN_UUID => $user->getId()])
            ->update([
                self::COLUMN_CPF => $user->getCpf(),
                self::COLUMN_UPDATED_AT => $user->getDateEdition(),
            ])
        ;
    }

    public function editEmail(User $user): void
    {
        DB::table(self::TABLE_NAME)
            ->where([self::COLUMN_UUID => $user->getId()])
            ->update([
                self::COLUMN_EMAIL => $user->getEmail(),
                self::COLUMN_UPDATED_AT => $user->getDateEdition(),
            ])
        ;
    }
}
