<?php

namespace App\Domain\User;

use App\Domain\Cpf\Cpf;
use App\Exceptions\DataValidationException;

class UserDataValidator implements UserDataValidatorInterface
{
    private const ID_MAX_LEGTH = 36;
    private const NAME_MAX_LEGTH = 100;
    private const EMAIL_MAX_LEGTH = 100;

    private const UUID_REGEX = '/[0-9a-fA-F]{8}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{12}/';

    public function validateId(string $id): void
    {
        /*
            TODO: validate id using UUID v4 pattern
        */
        if (!preg_match(self::UUID_REGEX, $id)) {
            throw new DataValidationException('The user id is not valid');
        }
    }

    public function validateName(string $name): void
    {
        if (empty($name)) {
            throw new DataValidationException('The user name cannot be empty');
        }

        if (strlen($name) > self::NAME_MAX_LEGTH) {
            throw new DataValidationException('The user name exceeds the max length');
        }
    }

    public function validateEmail(string $email): void
    {
        if (empty($email)) {
            throw new DataValidationException('The user email cannot be empty');
        }

        if (strlen($email) > self::EMAIL_MAX_LEGTH) {
            throw new DataValidationException('The user email exceeds the max length');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new DataValidationException('The user email is not valid');
        }
    }

    public function validateCpf(string $cpf): void
    {
        $trimmedCpf = trim($cpf);

        if (empty($trimmedCpf)) {
            throw new DataValidationException('The user cpf cannot be empty');
        }

        if (preg_match('/[^\d]/', $trimmedCpf)) {
            throw new DataValidationException('The user cpf is not valid');
        }

        $formattedCpf = preg_replace('/\D/', '', $trimmedCpf);

        if (strlen($formattedCpf) !== 11 || !is_numeric($formattedCpf) || !(new Cpf($formattedCpf))->isValid()) {
            throw new DataValidationException('The user cpf is not valid');
        }
    }


    public function validateDateCreation(string $dateCreation): void
    {
        if (empty($dateCreation)) {
            throw new DataValidationException('The user date creation cannot be empty');
        }

        if (!$this->isValidDate($dateCreation)) {
            throw new DataValidationException('The user date creation is not in a valid format');
        }
    }

    public function validateDateEdition(string $dateEdition): void
    {
        if (empty($dateEdition)) {
            throw new DataValidationException('The user date edition cannot be empty');
        }

        if (!$this->isValidDate($dateEdition)) {
            throw new DataValidationException('The user date edition is not in a valid format');
        }
    }

    private function isValidDate(string $date): bool
    {
        $dateTime = \DateTime::createFromFormat('Y-m-d H:i:s', $date);
        return $dateTime && $dateTime->format('Y-m-d H:i:s') === $date;
    }
}
