<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectGuestsTo(function (Request $request): ?string {
            if ($request->is('api/*')) {
                return null;
            }

            return '/';
        });

        $middleware->api(append: [
            \App\Http\Middleware\ApiResponseMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (ValidationException $exception, Request $request) {
            if (! $request->is('api/*')) {
                return null;
            }

            return response()->json([
                'code' => 422,
                'message' => '参数验证失败',
                'data' => [
                    'errors' => $exception->errors(),
                ],
            ], 422);
        });

        $exceptions->render(function (AuthenticationException $exception, Request $request) {
            if (! $request->is('api/*')) {
                return null;
            }

            return response()->json([
                'code' => 401,
                'message' => '未授权访问',
                'data' => null,
            ], 401);
        });

        $exceptions->render(function (\Throwable $exception, Request $request) {
            if (! $request->is('api/*')) {
                return null;
            }

            $status = $exception instanceof HttpExceptionInterface ? $exception->getStatusCode() : 500;
            $message = $status >= 500 ? '服务器内部错误' : ($exception->getMessage() ?: '请求失败');

            return response()->json([
                'code' => $status,
                'message' => $message,
                'data' => null,
            ], $status);
        });
    })->create();
