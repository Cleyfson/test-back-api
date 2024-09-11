<?php

namespace Tests\Http\Swagger;

use Tests\TestCase;

class SwaggerHttpTest extends TestCase
{
    public function testShouldCorrectlyReturnApiDocs(): void
    {
        $response = $this
            ->call(
                'GET',
                '/api-swagger'
            )
        ;

        $response->assertStatus(self::HTTP_SUCCESS_STATUS);
        $response->assertJson([
            'openapi' => '3.0.0'
        ]);
    }
}
