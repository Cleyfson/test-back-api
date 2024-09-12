<?php

namespace Tests\Http\User;

use App\Models\User;
use Tests\TestCase;
use Faker;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\DB;

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
            'cpf' => self::VALID_CPF,
            'is_credit_eligible' => $this->faker->randomElement([0, 1]),
        ]);
    }

    public function testShouldCorrectlyReturnAllUsersThatAreNotDeleted(): void
    {
        $response = $this
            ->call(
                'GET',
                '/user'
            )
        ;

        $response->assertStatus(self::HTTP_SUCCESS_STATUS);
        $response->assertJson([
            [
                'id' => $this->user->uuid,
                'name' => $this->user->name,
                'email' => $this->user->email,
                'cpf' => $this->user->cpf
            ]
        ]);
    }

    public function testShouldCorrectlyReturnUserById(): void
    {
        $response = $this
            ->call(
                'GET',
                "/user/{$this->user->uuid}"
            )
        ;

        $response->assertStatus(self::HTTP_SUCCESS_STATUS);
        $response->assertJson([
            'id' => $this->user->uuid,
            'name' => $this->user->name,
            'email' => $this->user->email,
            'cpf' => $this->user->cpf,
            'is_credit_eligible' => $this->user->is_credit_eligible
        ]);
    }

    public function testShouldSoftDeleteUser(): void
    {
        $userBeforeDelete = DB::table('user')->where('uuid', $this->user->uuid)->first();
        $this->assertNull($userBeforeDelete->deleted_at);

        $response = $this->call(
            'DELETE',
            "/user/{$this->user->uuid}"
        );

        $response->assertStatus(204);

        $userAfterDelete = DB::table('user')->where('uuid', $this->user->uuid)->first();
        $this->assertNotNull($userAfterDelete->deleted_at);
    }

}
