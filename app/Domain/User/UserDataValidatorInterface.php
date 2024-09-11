<?php

namespace App\Domain\User;

interface UserDataValidatorInterface
{
    public function validateId(string $id): void;
    public function validateName(string $name): void;
    public function validateEmail(string $email): void;
    public function validateCpf(string $cpf): void;
    public function validateDateCreation(string $dateCreation): void;
    public function validateDateEdition(string $dateEdition): void;
}
