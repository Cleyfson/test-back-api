<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    public const HTTP_SUCCESS_STATUS = 200;
    public const HTTP_CREATED_STATUS = 201;
    public const HTTP_BAD_REQUEST_STATUS = 400;

    public function buildBadRequestResponse(string $errorMessage): JsonResponse
    {
        return response()->json(
            ['bad_request' => $errorMessage],
            self::HTTP_BAD_REQUEST_STATUS
        );
    }

    public function buildCreatedResponse(mixed $data): JsonResponse
    {
        return response()->json(
            $data,
            self::HTTP_CREATED_STATUS,
            [],
            JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT
        );
    }

    public function buildSuccessResponse(mixed $data): JsonResponse
    {
        return response()->json(
            $data,
            self::HTTP_SUCCESS_STATUS,
            [],
            JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT
        );
    }
}
