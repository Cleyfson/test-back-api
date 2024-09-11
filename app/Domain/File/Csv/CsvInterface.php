<?php

namespace App\Domain\File\Csv;

interface CsvInterface
{
    public function buildAssociativeArrayFromContent(): array;
    public function setAssociativeContent(array $associativeContent): CsvInterface;
    public function getAssociativeContent(): array;
    public function setExpectedHeaders(array $expectedHeaders): CsvInterface;
    public function buildFromAssociativeContent(): void;
    public function getContent(): string;
}
