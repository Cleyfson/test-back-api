<?php

namespace Tests\Unit\User;

use App\Domain\User\User;
use App\Domain\User\UserDataValidator;
use App\Exceptions\DataValidationException;
use App\Infra\File\Csv\Csv;
use App\Infra\Memory\UserMemory;
use Tests\TestCase;
use Faker;

class UserTest extends TestCase
{
    private const VALID_CPF = '48472338088';
    private const INVALID_CPF_WITH_SPECIAL_CHARACTERS = '484.723.380-88';
    private const INVALID_CPF = '48992300088';

    private Faker\Generator $faker;

    public function setUp(): void
    {
        parent::setUp();

        $this->faker = Faker\Factory::create();
    }

    public function testShouldCorrectlyCreateUser(): void
    {
        $user = (new User(new UserMemory()))
            ->setDataValidator(new UserDataValidator())
            ->setId($this->faker->uuid())
            ->setName($this->faker->name())
            ->setEmail($this->faker->email())
            ->setCpf(self::VALID_CPF)
        ;

        $this->assertNotEmpty($user->getName());
    }

    public function testShouldThrowAnExceptionWhenTryToSetInvalidId(): void
    {
        $user = (new User(new UserMemory()))->setDataValidator(new UserDataValidator());

        $this->expectException(DataValidationException::class);
        $this->expectExceptionMessage('The user id is not valid');

        $user->setId('invalid-id');
    }

    public function testShouldThrowAnExceptionWhenTryToSetTooLongId(): void
    {
        $user = (new User(new UserMemory()))->setDataValidator(new UserDataValidator());

        $this->expectException(DataValidationException::class);
        $this->expectExceptionMessage('The user id is not valid');

        $user->setId('this-id-is-not-compatible-with-the-method-validation');
    }

    public function testShouldThrowAnExceptionWhenTryToSetEmptyId(): void
    {
        $user = (new User(new UserMemory()))->setDataValidator(new UserDataValidator());

        $this->expectException(DataValidationException::class);
        $this->expectExceptionMessage('The user id is not valid');

        $user->setId('');
    }

    public function testShouldThrowAnExceptionWhenTryToSetTooLongName(): void
    {
        $user = (new User(new UserMemory()))->setDataValidator(new UserDataValidator());

        $this->expectException(DataValidationException::class);
        $this->expectExceptionMessage('The user name exceeds the max length');

        $user->setName('this-name-is-not-compatible-with-the-method-validation-this-name-is-not-compatible-with-the-method-validation');
    }

    public function testShouldThrowAnExceptionWhenTryToSetEmptyName(): void
    {
        $user = (new User(new UserMemory()))->setDataValidator(new UserDataValidator());

        $this->expectException(DataValidationException::class);
        $this->expectExceptionMessage('The user name cannot be empty');

        $user->setName('');
    }

    public function testShouldThrowAnExceptionWhenTryToSetInvalidEmail(): void
    {
        $user = (new User(new UserMemory()))->setDataValidator(new UserDataValidator());

        $this->expectException(DataValidationException::class);
        $this->expectExceptionMessage('The user email is not valid');

        $user->setEmail('invalid-email');
    }

    public function testShouldThrowAnExceptionWhenTryToSetTooLongEmail(): void
    {
        $user = (new User(new UserMemory()))->setDataValidator(new UserDataValidator());

        $this->expectException(DataValidationException::class);
        $this->expectExceptionMessage('The user email exceeds the max length');

        $user->setEmail('someemailwithalotofcharacterssomeemailwithalotofcharacterssomeemailwithalotofcharacterssomeemailwithalotofcharacters@justtothrowexception.com');
    }

    public function testShouldThrowAnExceptionWhenTryToSetEmptyEmail(): void
    {
        $user = (new User(new UserMemory()))->setDataValidator(new UserDataValidator());

        $this->expectException(DataValidationException::class);
        $this->expectExceptionMessage('The user email cannot be empty');

        $user->setEmail('');
    }

    public function testShouldThrowAnExceptionWhenTryToSetCpfWithSpecialCharacters(): void
    {
        $user = (new User(new UserMemory()))->setDataValidator(new UserDataValidator());

        $this->expectException(DataValidationException::class);
        $this->expectExceptionMessage('The user cpf is not valid');

        $user->setCpf(self::INVALID_CPF_WITH_SPECIAL_CHARACTERS);
    }

    public function testShouldThrowAnExceptionWhenTryToSetInvalidCpf(): void
    {
        $user = (new User(new UserMemory()))->setDataValidator(new UserDataValidator());

        $this->expectException(DataValidationException::class);
        $this->expectExceptionMessage('The user cpf is not valid');

        $user->setCpf(self::INVALID_CPF);
    }

    public function testShouldThrowAnExceptionWhenTryToSetEmptyCpf(): void
    {
        $user = (new User(new UserMemory()))->setDataValidator(new UserDataValidator());

        $this->expectException(DataValidationException::class);
        $this->expectExceptionMessage('The user cpf cannot be empty');

        $user->setCpf('');
    }

    public function testShouldThrowAnExceptionWhenTryToSetNonNumericCpf(): void
    {
        $user = (new User(new UserMemory()))->setDataValidator(new UserDataValidator());

        $this->expectException(DataValidationException::class);
        $this->expectExceptionMessage('The user cpf is not valid');

        $user->setCpf('non-numeric-cpf');
    }

    public function testShouldThrowAnExceptionWhenTryToSetEmptyDateCreation(): void
    {
        $user = (new User(new UserMemory()))->setDataValidator(new UserDataValidator());

        $this->expectException(DataValidationException::class);
        $this->expectExceptionMessage('The user date creation cannot be empty');

        $user->setDateCreation('');
    }

    public function testShouldCorrectlySetDateCreation(): void
    {
        $user = (new User(new UserMemory()))->setDataValidator(new UserDataValidator());

        $user->setDateCreation('2023-12-29 15:29:00');

        $this->assertNotEmpty($user->getDateCreation());
    }

    public function testShouldThrowAnExceptionWhenTryToSetDateCreationInInvalidFormat(): void
    {
        $user = (new User(new UserMemory()))->setDataValidator(new UserDataValidator());

        $this->expectException(DataValidationException::class);
        $this->expectExceptionMessage('The user date creation is not in a valid format');

        $user->setDateCreation('invalid-date-format');
    }

    public function testShouldCorrectlySetDateEdition(): void
    {
        $user = (new User(new UserMemory()))->setDataValidator(new UserDataValidator());

        $user->setDateEdition('2023-12-30 16:30:00');

        $this->assertNotEmpty($user->getDateCreation());
    }

    public function testShouldThrowAnExceptionWhenTryToSetEmptyDateEdition(): void
    {
        $user = (new User(new UserMemory()))->setDataValidator(new UserDataValidator());

        $this->expectException(DataValidationException::class);
        $this->expectExceptionMessage('The user date edition cannot be empty');

        $user->setDateEdition('');
    }

    public function testShouldThrowAnExceptionWhenTryToSetDateEditionInInvalidFormat(): void
    {
        $user = (new User(new UserMemory()))->setDataValidator(new UserDataValidator());

        $this->expectException(DataValidationException::class);
        $this->expectExceptionMessage('The user date edition is not in a valid format');

        $user->setDateEdition('invalid-date-format');
    }

    public function testShouldThrowAnExceptionWhenTryToCreateFromBatchWithInvalidArray(): void
    {
        $batch = [new Csv()];

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('The users array must have only users');

        (new User(new UserMemory()))->createFromBatch($batch);
    }

    public function testShouldCorrectlyCreateFromBatch(): void
    {
        $user = (new User(new UserMemory()))
            ->setDataValidator(new UserDataValidator())
            ->setId($this->faker->uuid())
            ->setName($this->faker->name())
            ->setEmail($this->faker->email())
            ->setCpf(self::VALID_CPF)
        ;

        (new User(new UserMemory()))->createFromBatch([$user]);

        $this->assertNotEmpty($user->getName());
    }
}
