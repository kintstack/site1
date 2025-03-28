<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Http\Response;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        // Handle HTTP exceptions
        if ($exception instanceof HttpException) {
            $code = $exception->getStatusCode();
            $message = $exception->getMessage();
            return $this->errorResponse($message, $code);
        }

        // Handle ModelNotFoundException
        if ($exception instanceof ModelNotFoundException) {
            $model = strtolower(class_basename($exception->getModel()));
            return $this->errorResponse(
                "No instance of {$model} found with the given ID", 
                Response::HTTP_NOT_FOUND
            );
        }

        // Handle ValidationException
        if ($exception instanceof ValidationException) {
            $errors = $exception->validator->errors()->getMessages();
            return $this->errorResponse($errors, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Handle AuthorizationException
        if ($exception instanceof AuthorizationException) {
            return $this->errorResponse(
                $exception->getMessage(), 
                Response::HTTP_FORBIDDEN
            );
        }

        // Handle AuthenticationException
        if ($exception instanceof \Illuminate\Auth\AuthenticationException) {
            return $this->errorResponse(
                $exception->getMessage(), 
                Response::HTTP_UNAUTHORIZED
            );
        }

        // Development mode debugging
        if (env('APP_DEBUG', false)) {
            return parent::render($request, $exception);
        }

        return $this->errorResponse('Unexpected error. Try later', Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * Custom error response method.
     *
     * @param string|array $message
     * @param int $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    protected function errorResponse($message, $statusCode)
    {
        return response()->json([
            'error' => $message,
            'status' => $statusCode
        ], $statusCode);
    }
}
