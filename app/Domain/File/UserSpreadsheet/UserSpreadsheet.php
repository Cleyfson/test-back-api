<?php

namespace App\Domain\File\UserSpreadsheet;

use app\Domain\File\Csv\CsvInterface;
use App\Domain\User\User;
use App\Domain\User\UserDataValidator;
use App\Domain\User\UserPersistenceInterface;
use App\Domain\Uuid\UuidGeneratorInterface;
use App\Exceptions\DataValidationException;
use App\Exceptions\DuplicatedDataException;
use App\Exceptions\UserSpreadsheetException;

class UserSpreadsheet
{
    public const SPREADSHEET_LINE_NUMBER = 'line_number';

    public const NAME_HEADER = 'name';
    public const CPF_HEADER = 'cpf';
    public const EMAIL_HEADER = 'email';

    public const EXPECTED_HEADERS = [
        self::NAME_HEADER,
        self::CPF_HEADER,
        self::EMAIL_HEADER,
    ];

    private array $users;

    private UuidGeneratorInterface $uuidGenerator;
    private UserPersistenceInterface $userPersistence;
    private CsvInterface $csv;

    public function setUuidGenerator(UuidGeneratorInterface $uuidGenerator): UserSpreadsheet
    {
        $this->uuidGenerator = $uuidGenerator;

        return $this;
    }

    public function setUserPersistence(UserPersistenceInterface $userPersistence): UserSpreadsheet
    {
        $this->userPersistence = $userPersistence;

        return $this;
    }

    public function setUsers(array $users): UserSpreadsheet
    {
        $this->users = $users;

        return $this;
    }

    public function setCsv(CsvInterface $csv): UserSpreadsheet
    {
        $this->csv = $csv;

        return $this;
    }

    public function buildUsersFromContent(): array
    {
        $this
            ->csv
            ->setExpectedHeaders(self::EXPECTED_HEADERS)
            ->buildAssociativeArrayFromContent()
        ;

        $usersFromContent = $this->buildUsers();

        return $usersFromContent;
    }

    private function buildUsers(): array
    {
        $users = [];

        foreach ($this->csv->getAssociativeContent() as $userData) {
            $users[] = $this->buildUserFromUserData($userData);
        }

        return $users;
    }

    private function buildUserFromUserData(array $userData): User
    {
        try {
            $user = (new User($this->userPersistence))
                ->setDataValidator(new UserDataValidator())
                ->setName($userData[self::NAME_HEADER])
                ->setCpf($userData[self::CPF_HEADER])
                ->setEmail($userData[self::EMAIL_HEADER])
                ->setUuidGenerator($this->uuidGenerator)
                ->generateId()
                ->setDateCreation(date('Y-m-d H:i:s'))
            ;

            $user->checkAlreadyCreatedCpf();
            $user->checkAlreadyCreatedEmail();

            return $user;
        } catch (DataValidationException| DuplicatedDataException $e) {
            $lineNumber = $userData[self::SPREADSHEET_LINE_NUMBER];

            throw new UserSpreadsheetException("Spreadsheet error: line $lineNumber | {$e->getMessage()}");
        }
    }

    public function buildContentFromUsers(): string
    {
        $associativeContent = $this->buildAssociativeContentFromUsers();

        $this
            ->csv
            ->setExpectedHeaders(self::EXPECTED_HEADERS)
            ->setAssociativeContent($associativeContent)
        ;

        $this->csv->buildFromAssociativeContent();

        return $this->csv->getContent();
    }

    private function buildAssociativeContentFromUsers(): array
    {
        $associativeContent = [];

        foreach ($this->users as $user) {
            $associativeContent[] = [
                self::NAME_HEADER => $user->getName(),
                self::CPF_HEADER => $user->getCpf(),
                self::EMAIL_HEADER => $user->getEmail()
            ];
        }

        return $associativeContent;
    }
}
