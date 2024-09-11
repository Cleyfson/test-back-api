<?php

namespace Tests\Http\User;

use App\Models\User;
use Tests\TestCase;
use Faker;
use Laravel\Lumen\Testing\DatabaseMigrations;

class UserHttpTest extends TestCase
{
    use DatabaseMigrations;

    private const VALID_CPF = '48472338088';

    private Faker\Generator $faker;
    private User $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->faker = Faker\Factory::create();

        $this->user = User::create([
            'uuid' => $this->faker->uuid(),
            'name' => $this->faker->name(),
            'email' => $this->faker->email(),
            'cpf' => self::VALID_CPF
        ]);
    }

    public function testShouldCorrectlyReturnAllUsersThatAreNotDeleted(): void
    {
        $response = $this
            ->call(
                'GET',
                '/users'
            )
        ;

        $response->assertStatus(self::HTTP_SUCCESS_STATUS);
        $response->assertJson([
            [
                'uuid' => $this->user->uuid,
                'name' => $this->user->name,
                'email' => $this->user->email,
                'cpf' => $this->user->cpf
            ]
        ]);
    }
}
