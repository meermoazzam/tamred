<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ResponseManager {

    public function jsonSuccess (int $code, string $message = 'Success', mixed $data = []): JsonResponse
    {
        return response()->json([
            'status' => true,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    public function jsonError (int $code, string $message = 'Error', mixed $data = []): JsonResponse
    {
        return response()->json([
            'status' => false,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    public function jsonException (string $message): JsonResponse
    {
        // add a logger here if you want to save exception message
        return response()->json([
            'status' => false,
            'message' => $message,
        ], 500);
    }
}
