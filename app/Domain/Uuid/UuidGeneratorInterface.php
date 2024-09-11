<?php

namespace App\Domain\Uuid;

interface UuidGeneratorInterface
{
    public function generate(): string;
}
