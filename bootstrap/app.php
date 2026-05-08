<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (Throwable $e, Request $request) {
            if ($request->is('api/*')) {
                if ($e instanceof \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException) {
                    return response()->json([
                        'success' => false,
                        'message' => "The {$request->method()} method is not supported for this endpoint.",
                        'error' => 'Invalid HTTP method. This endpoint expects POST request.',
                        'error_type' => 'MethodNotAllowed',
                        'hint' => 'Please use the correct HTTP method and try again.',
                        'current_method' => $request->method(),
                        'url' => $request->fullUrl(),
                    ], 405);
                }
                if ($e instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
                    return response()->json([
                        'success' => false,
                        'message' => 'The requested endpoint was not found.',
                        'error' => 'Invalid API route.',
                        'error_type' => 'RouteNotFound',
                        'hint' => 'Please verify the URL and try again.',
                        'current_method' => $request->method(),
                        'url' => $request->fullUrl(),
                    ], 404);
                }
                if ($e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Resource not found.',
                        'error' => 'The requested resource does not exist.',
                        'error_type' => 'ResourceNotFound',
                        'hint' => 'Please verify the ID or identifier you are trying to access.',
                    ], 404);
                }
                if ($e instanceof \Illuminate\Validation\ValidationException) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Validation failed.',
                        'error' => 'Please check your input data.',
                        'error_type' => 'ValidationError',
                        'errors' => $e->errors(),
                        'hint' => 'Correct the validation errors and resubmit.',
                    ], 422);
                }
                if ($e instanceof \Illuminate\Auth\AuthenticationException) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Unauthenticated.',
                        'error' => 'You need to be logged in to access this resource.',
                        'error_type' => 'AuthenticationError',
                        'hint' => 'Please login first or provide a valid token.',
                    ], 401);
                }
                if ($e instanceof \Illuminate\Auth\Access\AuthorizationException) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Forbidden.',
                        'error' => 'You do not have permission to access this resource.',
                        'error_type' => 'AuthorizationError',
                        'hint' => 'Contact administrator if you believe this is a mistake.',
                    ], 403);
                }
                if ($e instanceof \Laravel\Sanctum\Exceptions\MissingAbilityException) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid or expired token.',
                        'error' => 'Your authentication token is invalid or has expired.',
                        'error_type' => 'TokenError',
                        'hint' => 'Please login again to get a new token.',
                    ], 401);
                }
                if ($e instanceof \Illuminate\Database\QueryException) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Database error occurred.',
                        'error' => 'Unable to process your request due to a database issue.',
                        'error_type' => 'DatabaseError',
                        'hint' => 'Please try again later or contact support.',
                    ], 500);
                }
                if ($e instanceof \Illuminate\Http\Exceptions\ThrottleRequestsException) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Too many requests.',
                        'error' => 'You have exceeded the rate limit.',
                        'error_type' => 'RateLimitExceeded',
                        'hint' => 'Please wait a moment before trying again.',
                    ], 429);
                }
                $isDebugMode = config('app.debug');                
                $response = [
                    'success' => false,
                    'message' => $isDebugMode ? $e->getMessage() : 'An unexpected error occurred.',
                    'error_type' => class_basename($e),
                    'hint' => $isDebugMode ? 'Check your request or contact support.' : 'Please try again later.',
                ];
                if ($isDebugMode) {
                    $response['debug'] = [
                        'file' => $e->getFile(),
                        'line' => $e->getLine(),
                        'trace' => collect($e->getTrace())->take(3)->toArray(),
                    ];
                    $response['current_method'] = $request->method();
                    $response['url'] = $request->fullUrl();
                }                
                $statusCode = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500;                
                return response()->json($response, $statusCode);
            }
        });
    })->create();
