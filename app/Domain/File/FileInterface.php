<?php

namespace App\Domain\File;

interface FileInterface
{
    public function setDataValidator(FileDataValidatorInterface $dataValidator): FileInterface;
    public function getDataValidator(): FileDataValidatorInterface;
    public function setMimeType(string $mimeType): FileInterface;
    public function getMimeType(): string;
    public function setContent(string $content): File;
    public function getContent(): string;
    public function setSizeInBytes(string $sizeInBytes): FileInterface;
    public function getSizeInBytes(): string;
}
