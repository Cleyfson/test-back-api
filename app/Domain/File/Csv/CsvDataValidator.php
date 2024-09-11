<?php

namespace App\Domain\File\Csv;

use App\Domain\File\FileDataValidatorInterface;
use App\Exceptions\DataValidationException;

class CsvDataValidator implements FileDataValidatorInterface
{
    private const VALID_MIME_TYPE = 'text/csv';
    private const MAX_SIZE_IN_BYTES = 1000000;
    private const MIN_SIZE_IN_BYTES = 1;

    public function validateMimeType(string $mimeType): void
    {
        $trimmedMimeType = trim($mimeType);

        if (empty($trimmedMimeType)) {
            throw new DataValidationException('The file mimeType cannot be empty');
        }

        if ($trimmedMimeType !== self::VALID_MIME_TYPE) {
            throw new DataValidationException('The file type is not valid');
        }
    }

    public function validateContent(string $content): void
    {
        $trimmedContent = trim($content);

        if (empty($trimmedContent)) {
            throw new DataValidationException('The file Content cannot be empty');
        }
    }

    public function validateSizeInBytes(int $sizeInBytes): void
    {
        if (
            $sizeInBytes < self::MIN_SIZE_IN_BYTES
            || $sizeInBytes > self::MAX_SIZE_IN_BYTES
        ) {
            throw new DataValidationException('The file size is not valid');
        }
    }
}
