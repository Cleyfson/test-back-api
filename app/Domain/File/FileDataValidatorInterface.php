<?php

namespace App\Domain\File;

interface FileDataValidatorInterface
{
    public function validateMimeType(string $mimeType): void;
    public function validateContent(string $content): void;
    public function validateSizeInBytes(int $sizeInBytes): void;
}
