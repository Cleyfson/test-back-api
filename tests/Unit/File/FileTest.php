<?php

namespace Tests\Unit\File;

use App\Domain\File\Csv\CsvDataValidator;
use App\Domain\File\File;
use Tests\TestCase;

class FileTest extends TestCase
{
    public function testShouldCorrectlyCreateFile(): void
    {
        $file = (new File())
            ->setDataValidator(new CsvDataValidator())
            ->setMimeType('text/csv')
            ->setSizeInBytes(1000000)
            ->setContent("
                name,cpf,email
                Paolo Maldini,29983872099,some@email.com
                Andrea Pirlo,56663819038,some2@email.com
            ")
        ;

        $this->assertNotEmpty($file->getContent());
    }

    public function testShouldThrowAnExceptionWhenTryToSetEmptyMimeType(): void
    {
        $file = (new File())->setDataValidator(new CsvDataValidator());

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('The file mimeType cannot be empty');

        $file->setMimeType('');
    }

    public function testShouldThrowAnExceptionWhenTryToSetInvalidMimeType(): void
    {
        $file = (new File())->setDataValidator(new CsvDataValidator());

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('The file type is not valid');

        $file->setMimeType('application/json');
    }

    public function testShouldThrowAnExceptionWhenTryToSetEmptyContent(): void
    {
        $file = (new File())->setDataValidator(new CsvDataValidator());

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('The file Content cannot be empty');

        $file->setContent('');
    }

    public function testShouldThrowAnExceptionWhenTryToSetSmallSizeInBytes(): void
    {
        $file = (new File())->setDataValidator(new CsvDataValidator());

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('The file size is not valid');

        $file->setSizeInBytes(0);
    }

    public function testShouldThrowAnExceptionWhenTryToSetTooBigSizeInBytes(): void
    {
        $file = (new File())->setDataValidator(new CsvDataValidator());

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('The file size is not valid');

        $file->setSizeInBytes(1000001);
    }
}
