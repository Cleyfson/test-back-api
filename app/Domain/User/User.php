<?php

namespace App\Domain\User;

use App\Domain\Uuid\UuidGeneratorInterface;
use App\Exceptions\DuplicatedDataException;
use App\Exceptions\InvalidUserObjectException;
use App\Exceptions\UserNotFoundException;
use DateTime;

class User
{
    private const MINIMUM_MONTHS_FOR_CREDIT_ELIGIBILITY = '6';

    private string $id;
    private string $name;
    private string $email;
    private string $cpf;
    private string $dateCreation;
    private string $dateEdition;
    private string $isCreditEligible;

    private UserDataValidatorInterface $dataValidator;
    private UuidGeneratorInterface $uuidGenerator;
    private UserPersistenceInterface $persistence;

    public function __construct(UserPersistenceInterface $persistence)
    {
        $this->persistence = $persistence;
    }

    public function setDataValidator(UserDataValidatorInterface $dataValidator): User
    {
        $this->dataValidator = $dataValidator;

        return $this;
    }

    public function getDataValidator(): UserDataValidatorInterface
    {
        return $this->dataValidator;
    }

    public function setUuidGenerator(UuidGeneratorInterface $uuidGenerator): User
    {
        $this->uuidGenerator = $uuidGenerator;

        return $this;
    }

    public function setId(string $id): User
    {
        $this->getDataValidator()->validateId($id);

        $this->id = $id;

        return $this;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setName(string $name): User
    {
        $this->getDataValidator()->validateName($name);

        $this->name = $name;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setEmail(string $email): User
    {
        $this->getDataValidator()->validateEmail($email);

        $this->email = $email;

        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setCpf(string $cpf): User
    {
        $this->getDataValidator()->validateCpf($cpf);

        $this->cpf = $cpf;

        return $this;
    }

    public function getCpf(): string
    {
        return $this->cpf;
    }

    public function setDateCreation(string $dateCreation): User
    {
        $this->getDataValidator()->validateDateCreation($dateCreation);

        $this->dateCreation = $dateCreation;

        return $this;
    }

    public function getDateCreation(): string
    {
        return $this->dateCreation;
    }

    public function setDateEdition(string $dateEdition): User
    {
        $this->getDataValidator()->validateDateEdition($dateEdition);

        $this->dateEdition = $dateEdition;

        return $this;
    }

    public function getDateEdition(): string
    {
        return $this->dateEdition;
    }

    public function generateId(): User
    {
        $this->id = $this->uuidGenerator->generate();

        return $this;
    }

    public function setIsCreditEligible(int $isCreditEligible): self
    {
        $this->isCreditEligible = $isCreditEligible;

        return $this;
    }

    public function getIsCreditEligible(): int
    {
        return $this->isCreditEligible;
    }

    public function createFromBatch(array $users): void
    {
        $this->checkUsers($users);

        foreach ($users as $user) {
            $this->persistence->create($user);
        }
    }

    private function checkUsers(array $users): void
    {
        foreach($users as $user) {
            if ($user::class !== $this::class) {
                throw new InvalidUserObjectException('The users array must have only users');
            }
        }
    }

    public function checkAlreadyCreatedCpf(): void
    {
        if ($this->persistence->isCpfAlreadyCreated($this)) {
            throw new DuplicatedDataException('CPF already created');
        }
    }

    public function checkAlreadyCreatedEmail(): void
    {
        if ($this->persistence->isEmailAlreadyCreated($this)) {
            throw new DuplicatedDataException('Email already created');
        }
    }

    public function findAll(): array
    {
        return $this->persistence->findAll($this);
    }

    public function findById(string $id): User
    {
        $this->checkExistentId();

        $user = $this->persistence->findById($id);

        $admissionDate = new DateTime($user->getDateCreation());
        $currentDate = new DateTime();
        $interval = $currentDate->diff($admissionDate);
        $isCreditEligible = ($interval->m + ($interval->y * 12)) >= self::MINIMUM_MONTHS_FOR_CREDIT_ELIGIBILITY ? 1 : 0;

        $user->setIsCreditEligible($isCreditEligible);

        return $user;
    }

    public function deleteUser(string $id): void
    {
        $this->checkExistentId();

        $this->persistence->delete($id);
    }

    public function editName(): void
    {
        $this->checkExistentId();

        $this->setDateEdition(date('Y-m-d H:i:s'));

        $this->persistence->editName($this);
    }

    private function checkExistentId(): void
    {
        if (!$this->persistence->isExistentId($this)) {
            throw new UserNotFoundException('The user does not exist');
        }
    }

    public function editCpf(): void
    {
        $this->checkExistentId();

        $this->checkAlreadyCreatedCpf();

        $this->setDateEdition(date('Y-m-d H:i:s'));

        $this->persistence->editCpf($this);
    }

    public function editEmail(): void
    {
        $this->checkExistentId();

        $this->checkAlreadyCreatedEmail();

        $this->setDateEdition(date('Y-m-d H:i:s'));

        $this->persistence->editEmail($this);
    }
}
