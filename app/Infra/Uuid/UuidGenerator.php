<?php

namespace App\Infra\Uuid;

use App\Domain\Uuid\UuidGeneratorInterface;
use Faker;

class UuidGenerator implements UuidGeneratorInterface
{
    public function generate(): string
    {
        $faker = Faker\Factory::create();

        return $faker->uuid();
    }
}
