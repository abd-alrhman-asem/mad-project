<?php


namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
        });
    }

    protected function HandleException(Throwable $exception): JsonResponse
    {
        if ($exception instanceof ModelNotFoundException) {
            return response()->json(['error' => 'Resource not found'], 404);
        }

        if ($exception instanceof NotFoundHttpException) {
            return response()->json(['error' => 'Resource not found'], 404);
        }

        if ($exception instanceof AuthenticationException) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        if ($exception instanceof ThrottleRequestsException) {
            return response()->json(['error' => 'Too many requests'], 429);
        }

        if ($exception instanceof AuthorizationException) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        if ($exception instanceof QueryException) {
            return response()->json(['error' => 'Database error'], 500);
        }

        if ($exception instanceof ValidationException) {
            return response()->json([
                'error' => 'Validation error',
                'messages' => $exception->errors(),
            ], 422);
        }

        return response()->json(['error' => 'Server error'], 500);
    }

    public function render($request, Throwable $exception)
    {
        return $this->HandleException($exception);
    }
}
