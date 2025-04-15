<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class Handler extends ExceptionHandler
{

    public function render($request, Throwable $exception)
    {
        if ($exception instanceof UnauthorizedHttpException) {
            $previous = $exception->getPrevious();

            if ($previous instanceof TokenExpiredException) {
                return response()->json(['status' => false, 'message' => 'Token has expired'], 401);
            } elseif ($previous instanceof TokenInvalidException) {
                return response()->json(['status' => false, 'message' => 'Token is invalid'], 401);
            } elseif ($previous instanceof JWTException) {
                return response()->json(['status' => false, 'message' => 'Token is missing'], 401);
            }
        }

        if ($exception instanceof AuthenticationException) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 401);
        }

        return parent::render($request, $exception);
    }
    
    
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
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
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    
}
