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
        return strlen($this->cpf) === self::MAX_LENGTH;
    }

    private function isValidNumber(): bool
    {
        if (preg_match('/(\d)\1{10}/', $this->cpf)) {
            return false;
        }

        for ($t = 9; $t < 11; $t++) {
            $d = 0;
            for ($c = 0; $c < $t; $c++) {
                $d += $this->cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($this->cpf[$c] != $d) {
                return false;
            }
        }

        return true;
    }
}
