<?php

namespace App\Domain\File;

class File implements FileInterface
{
    private string $mimeType;
    private string $content;
    private int $sizeInBytes;

    private FileDataValidatorInterface $dataValidator;

    public function setDataValidator(FileDataValidatorInterface $dataValidator): File
    {
        $this->dataValidator = $dataValidator;

        return $this;
    }

    public function getDataValidator(): FileDataValidatorInterface
    {
        return $this->dataValidator;
    }

    public function setMimeType(string $mimeType): File
    {
        $this->getDataValidator()->validateMimeType($mimeType);

        $this->mimeType = $mimeType;

        return $this;
    }

    public function getMimeType(): string
    {
        return $this->mimeType;
    }

    public function setContent(string $content): File
    {
        $this->getDataValidator()->validateContent($content);

        $this->content = $content;

        return $this;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setSizeInBytes(string $sizeInBytes): File
    {
        $this->getDataValidator()->validateSizeInBytes($sizeInBytes);

        $this->sizeInBytes = $sizeInBytes;

        return $this;
    }

    public function getSizeInBytes(): string
    {
        return $this->sizeInBytes;
    }
}
