<?php

namespace App\Infra\Swagger;

use OpenApi\Annotations\OpenApi;
use OpenApi\Generator;

class Swagger
{
    private string $docBlocksPath;

    private OpenApi $openApiDocumentation;

    public function setDocBlocksPath(string $docBlocksPath): Swagger
    {
        $this->docBlocksPath = $docBlocksPath;

        return $this;
    }

    public function generateDocumentation(): OpenApi
    {
        $this->openApiDocumentation = Generator::scan([$this->docBlocksPath]);

        return $this->openApiDocumentation;
    }
}
