<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Exceptions\HttpResponseException;
use Spatie\FlareClient\Http\Exceptions\NotFound;
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

    protected function unauthenticated($request, AuthenticationException $exception): Response|JsonResponse|RedirectResponse
    {
        return response()->json(['message' => 'Unauthenticated'], 401);
    }

//    public function register(): void
//    {
//        $this->renderable(function (NotFoundHttpException $e,$request) {
//            if ($request->is('api/obtener-personas')) {
//                return response()->json([
//                    'status' => false,
//                    'message' => 'No se encontraron personas con ese nombre'
//                ], 404);
//            }
//        });
//    }
}
