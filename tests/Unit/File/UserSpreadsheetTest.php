<?php

namespace Tests\Unit\File;

use App\Domain\File\Csv\CsvDataValidator;
use App\Domain\File\UserSpreadsheet\UserSpreadsheet;
use App\Infra\File\Csv\Csv;
use App\Infra\Memory\UserMemory;
use App\Infra\Uuid\UuidGenerator;
use Tests\TestCase;

class UserSpreadsheetTest extends TestCase
{
    public function testShouldThrowAnExceptionWhenTryToReadCsvFileWithHeadersNamesDifferentFromTheExpected(): void
    {
        $userSpreadsheet = new UserSpreadsheet();

        $csv = new Csv();

        $csv
            ->setDataValidator(new CsvDataValidator())
            ->setMimeType('text/csv')
            ->setSizeInBytes(1000000)
            ->setContent("name2,cpf,email\nPaolo Maldini,29983872099,some@email.com\nAndrea Pirlo,56663819038,some2@email.com\n")
        ;

        $userSpreadsheet
            ->setUuidGenerator(new UuidGenerator())
            ->setUserPersistence(new UserMemory())
            ->setCsv($csv)
        ;

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('The csv file is not valid');

        $userSpreadsheet->buildUsersFromContent();
    }

    public function testShouldThrowAnExceptionWhenTryToReadCsvFileWithHeadersAmountDifferentFromTheExpected(): void
    {
        $userSpreadsheet = new UserSpreadsheet();

        $csv = new Csv();

        $csv
            ->setDataValidator(new CsvDataValidator())
            ->setMimeType('text/csv')
            ->setSizeInBytes(1000000)
            ->setContent("name,cpf\nPaolo Maldini,29983872099\nAndrea Pirlo,56663819038\n")
        ;

        $userSpreadsheet
            ->setUuidGenerator(new UuidGenerator())
            ->setUserPersistence(new UserMemory())
            ->setCsv($csv)
        ;

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('The csv file is not valid');

        $userSpreadsheet->buildUsersFromContent();
    }

    public function testShouldCorrectlyBuildUsersFromContent(): void
    {
        $userSpreadsheet = new UserSpreadsheet();

        $csv = new Csv();

        $csv
            ->setDataValidator(new CsvDataValidator())
            ->setMimeType('text/csv')
            ->setSizeInBytes(1000000)
            ->setContent("name,cpf,email\nPaolo Maldini,29983872099,some@email.com\nAndrea Pirlo,56663819038,some2@email.com\n")
        ;

        $userSpreadsheet
            ->setUuidGenerator(new UuidGenerator())
            ->setUserPersistence(new UserMemory())
            ->setCsv($csv)
        ;

        $users = $userSpreadsheet->buildUsersFromContent();

        $this->assertSame(
            count($users),
            2
        );
    }
}
