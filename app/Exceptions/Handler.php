<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $e)
    {
        if ($request->expectsJson()) {
            $response = [
                'error' => $e->getMessage(),
            ];
            $httpCode = 500;

            if ($e instanceof AuthenticationException) {
                $httpCode = 401;
            } elseif ($e instanceof AuthorizationException) {
                $httpCode = 403;
            } elseif ($e instanceof ModelNotFoundException || $e instanceof NotFoundHttpException) {
                $httpCode = 404;
            } elseif ($e instanceof TokenMismatchException) {
                $httpCode = 419;
            } elseif ($e instanceof ValidationException) {
                $httpCode = 422;
                $response['errors'] = $e->validator->errors();
            }

            return response()->json($response, $httpCode);
        }

        return parent::render($request, $e);
    }
}
