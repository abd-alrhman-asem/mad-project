<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
trait ApiResponseHandlerTrait
{


    protected function successResponse($data, $message = '', $status = 200): JsonResponse
    {
        return response()->json([
            'message' => $message,
            'data' => $data
        ], $status);
    }

    protected function errorResponse($message, $status = 400): JsonResponse
    {
        return response()->json([
            'message' => $message
        ], $status);
    }

    protected function successMessage($message, $status = 200): JsonResponse
    {
        return response()->json([
            'message' => $message
        ], $status);
    }
}
