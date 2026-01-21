<?php

namespace App\Traits;

trait ApiResponsesTrait
{

    public function successResponse(string $message = '', array $data = [], int $statusCode = 200)
    {
        return response()->json([
            'status' => 'success',
            'message' => $message ?? "success",
            'data' => $data,
            'errors' => [],
        ], $statusCode);
    }


    public function errorResponse(string $message, array $errors = [], int $statusCode = 404)
    {
        return response()->json([
            'status' => 'failed',
            'message' => $message ?? "failed",
            'data' => [],
            'errors' => $errors ?? [],
        ], $statusCode);
    }
}
