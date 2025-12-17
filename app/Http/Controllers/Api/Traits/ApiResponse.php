<?php

namespace App\Http\Controllers\Api\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    protected function success($data = null, string $message = 'Success', int $code = 200): JsonResponse
    {
        $payload = [
            'status' => 'success',
            'message' => $message,
        ];

        if ($data !== null) {
            if (is_array($data)) {
                $payload = array_merge($payload, $data);
            } else {
                $payload['data'] = $data;
            }
        }

        return response()->json($payload, $code);
    }

    protected function error(string $message = 'Error', int $code = 400, $errors = null): JsonResponse
    {
        $payload = [
            'status' => 'error',
            'message' => $message,
        ];

        if ($errors !== null) {
            $payload['errors'] = $errors;
        }

        return response()->json($payload, $code);
    }
}
