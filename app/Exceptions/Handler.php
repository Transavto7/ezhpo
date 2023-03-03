<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
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
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response| \Illuminate\Http\JsonResponse
     */
    public function render($request, Exception $exception) : Response
    {
        if ($request->expectsJson()) {
            switch (true) {
                case ($exception instanceof NotFoundHttpException):
                    return response(['message' => $exception->getMessage()], Response::HTTP_NOT_FOUND);
                    break;
                default:
                    return response($exception->getMessage(), Response::HTTP_BAD_REQUEST);
                    break;
            }
        }
        if ($this->isHttpException($exception)) {
            $status = $exception->getStatusCode();
            /** @var HttpExceptionInterface $exception */
            if (!($status >= 200 && $status < 300)) {
                return response()->view('pages.error', [], $status);
            }
        }

        return parent::render($request, $exception);
    }
}
