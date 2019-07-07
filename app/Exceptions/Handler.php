<?php

namespace App\Exceptions;

use Exception;
use Doctrine\ORM\EntityNotFoundException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Response as IlluminateResponse;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
        'currentPassword',
    ];

    /**
     * Report or log an exception.
     *
     * @param \Exception $exception
     *
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Exception $exception
     *
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        if ($exception instanceof AuthorizationException) {
            return $this->unauthorized($request, $exception);
        } elseif ($exception instanceof EntityNotFoundException) {
            throw  new NotFoundHttpException('Entity not found', $exception);
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
            return response()->json(['error' => 'Unauthorized.'], IlluminateResponse::HTTP_FORBIDDEN);
        }

        flash('Unauthorized')->error();

        return redirect()->route('home');
    }
}
