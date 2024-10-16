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

        // Customize the ThrottleRequestsException message here
        if ($exception instanceof ThrottleRequestsException) {
            return response()->json(['error' => 'You can only resend the code twice within 5 minutes.'], 429);
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

