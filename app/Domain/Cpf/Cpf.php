<?php

namespace App\Domain\Cpf;

/*
Original code to CPF validation:
https://gist.github.com/rafael-neri/ab3e58803a08cb4def059fce4e3c0e40
*/

class Cpf
{
    private const MAX_LENGTH = 11;

    private string $cpf;

    public function __construct(string $cpf)
    {
        $this->cpf = $cpf;
    }

    public function isValid(): bool
    {
        $isValidLength = $this->isValidLength();
        $isValidNumber = $this->isValidNumber();

        return $isValidLength && $isValidNumber;
    }

    private function isValidLength(): bool
    {
        return false;
    }

    private function isValidNumber(): bool
    {
        return false;
    }
}
