<?php

namespace App\Exceptions;

use Api\Exceptions\ApiException;
use App\Services\RollbarLogger;
use Dotenv\Exception\ValidationException;
use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Log;
use Psr\Log\LoggerInterface;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        \Illuminate\Auth\AuthenticationException::class,
        \Illuminate\Auth\Access\AuthorizationException::class,
        \Symfony\Component\HttpKernel\Exception\HttpException::class,
        \Illuminate\Database\Eloquent\ModelNotFoundException::class,
        \Illuminate\Session\TokenMismatchException::class,
//        \Illuminate\Validation\ValidationException::class,
    ];

    public function report(Exception $exception)
    {
        if ($this->shouldntReport($exception)) {
            return;
        }

        try {
            $logger = $this->container->make(LoggerInterface::class);
            RollbarLogger::logException($exception);
        } catch (Exception $ex) {
            throw $exception; // throw the original exception
        }

        $logger->error($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Exception $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
        Log::info($e);
        $e = $this->prepareException($e);
        if ($e instanceof HttpResponseException) {
            return $e->getResponse();
        } elseif ($e instanceof AuthenticationException) {
            return $this->unauthenticated($request, $e);
        } elseif ($e instanceof ValidationException || $e instanceof \Illuminate\Validation\ValidationException) {
            return $this->convertValidationExceptionToResponse($e, $request);
        } elseif ($e instanceof ApiException) {
            return $this->apiException($e);
        }

        return $this->prepareResponse($request, $e);
    }

    /**
     * Convert an authentication exception into an unauthenticated response.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Illuminate\Auth\AuthenticationException $exception
     * @return \Illuminate\Http\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            Log::info('expectsJson');
            return response()->json([], 401);
        }
        Log::info('headers');
        Log::info($request->headers->__toString());
        Log::info('not expectsJson');
        return redirect()->guest(route('login'));
    }


    /**
     * Prepare response containing exception render.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Exception $e
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function prepareResponse($request, Exception $e)
    {
        if ($request->expectsJson()) {
            return $this->jsonException($e);
        }


        if ($this->isHttpException($e)) {
            return $this->toIlluminateResponse($this->renderHttpException($e), $e);
        } else {
            return $this->toIlluminateResponse($this->convertExceptionToResponse($e), $e);
        }
    }

    protected function jsonException(Exception $e)
    {
        $code = $this->isHttpException($e) ? $e->getStatusCode() : 500;
        $data = app()->environment() == 'production' ? [] : [
            'error' => $e->getMessage(),
            'file' => $e->getFile() . ':' . $e->getLine(),
            'trace' => $e->getTraceAsString()
        ];
        return response()->json($data, $code);
    }

    protected function apiException(ApiException $e)
    {
        return response()->json([
            'error_code' => $e->getErrorCode(),

        ], $e->getHttpCode());
    }

    /**
     * Create a response object from the given validation exception.
     *
     * @param  \Illuminate\Validation\ValidationException $e
     * @param  \Illuminate\Http\Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function convertValidationExceptionToResponse(\Illuminate\Validation\ValidationException $e, $request)
    {
        if ($e->response) {
            return $e->response;
        }

        $errors = $e->validator->errors()->getMessages();

        if ($request->expectsJson()) {
            $data = app()->environment() == 'production' ? [] : $errors;
            return response()->json($data, 422);
        }

        return redirect()->back()->withInput(
            $request->input()
        )->withErrors($errors);
    }

}
