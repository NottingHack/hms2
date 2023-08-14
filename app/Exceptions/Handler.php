<?php

namespace App\Exceptions;

use Doctrine\ORM\EntityNotFoundException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Response as IlluminateResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
        'currentPassword',
    ];

    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Throwable $exception
     *
     * @return \Illuminate\Http\Response
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function render($request, Throwable $exception)
    {
        if ($exception instanceof AuthorizationException) {
            return $this->unauthorized($request, $exception);
        } elseif ($exception instanceof EntityNotFoundException) {
            throw new NotFoundHttpException('Entity not found', $exception);
        }

        return parent::render($request, $exception);
    }

    /**
     * Convert an unauthorized exception into an unauthorized response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Auth\AuthorizationException $exception
     *
     * @return \Illuminate\Http\Response
     */
    protected function unauthorized($request, AuthorizationException $exception)
    {
        if ($request->expectsJson()) {
            $data = [
                'errors' => [
                    [
                        'status' => IlluminateResponse::HTTP_FORBIDDEN,
                        'title'  => 'Unauthorized',
                        'detail' => $exception->getMessage(),
                    ],
                ],
            ];

            return response()->json($data, IlluminateResponse::HTTP_FORBIDDEN);
        }

        flash('Unauthorized')->error();

        return redirect()->route('home');
    }

    /**
     * Convert an authentication exception into a response.
     * Overridden to give JSON API style error.
     *
     * @param \Illuminate\Http\Request  $request
     * @param \Illuminate\Auth\AuthenticationException  $exception
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            $data = [
                'errors' => [
                    [
                        'status' => IlluminateResponse::HTTP_UNAUTHORIZED,
                        'title'  => 'Unauthenticated',
                        'detail' => $exception->getMessage(),
                    ],
                ],
            ];

            return response()->json($data, IlluminateResponse::HTTP_UNAUTHORIZED);
        }

        return redirect()->guest($exception->redirectTo() ?? route('login'));
    }
}
