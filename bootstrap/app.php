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
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // 启用 CORS 中间件
        $middleware->use([
            \Illuminate\Http\Middleware\HandleCors::class,
        ]);

        // 将登录、注册路由排除 CSRF 验证
        $middleware->validateCsrfTokens(except: [
            'vite/login',
            'vite/register',
        ]);

        $middleware->api(append: [
            \App\Http\Middleware\ApiResponseMiddleware::class,
        ]);
    })
    // 配置异常处理
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (ValidationException $exception, Request $request) {
            // 获取第一个错误信息
            $errors = $exception->errors();
            $firstError = '';

            foreach ($errors as $fieldErrors) {
                if (!empty($fieldErrors)) {
                    $firstError = $fieldErrors[0];
                    break;
                }
            }

            return response()->json([
                'code' => 422,
                'message' => $firstError ?: '参数验证失败',
                'data' => null,
            ], 422);
        });
        // 认证异常
        $exceptions->render(function (AuthenticationException $exception, Request $request) {
            return response()->json([
                'code' => 401,
                'message' => '未授权访问',
                'data' => null,
            ], 401);
        });
        // 其他异常
        $exceptions->render(function (\Throwable $exception, Request $request) {
            $status = $exception instanceof HttpExceptionInterface ? $exception->getStatusCode() : 500;
            $message = $status >= 500 ? '服务器内部错误' : ($exception->getMessage() ?: '请求失败');

            return response()->json([
                'code' => $status,
                'message' => $message,
                'data' => null,
            ], $status);
        });
    })->create();
