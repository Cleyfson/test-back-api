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
    }

    public function validateName(string $name): void
    {
        
    }

    public function validateEmail(string $email): void
    {
        
    }

    public function validateCpf(string $cpf): void
    {
        $trimmedCpf = trim($cpf);

        if (!(new Cpf($trimmedCpf))->isValid()) {
            throw new DataValidationException('The user cpf is not valid');
        }
    }

    public function validateDateCreation(string $dateCreation): void
    {
        
    }

    public function validateDateEdition(string $dateEdition): void
    {
       
    }
}
