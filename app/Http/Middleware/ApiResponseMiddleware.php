<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiResponseMiddleware {
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response {
        $response = $next($request);

        if ($response->getStatusCode() === 204) {
            return $response;
        }

        if (! $response instanceof JsonResponse) {
            return response()->json([
                'code' => $response->getStatusCode(),
                'message' => $response->isSuccessful() ? '请求成功' : '请求失败',
                'data' => $response->getContent(),
            ], $response->getStatusCode());
        }

        $status = $response->getStatusCode();
        $payload = $response->getData(true);

        if (is_array($payload) && array_key_exists('code', $payload)) {
            return $response;
        }

        if (is_array($payload) && array_key_exists('message', $payload) && array_key_exists('data', $payload)) {
            // 构建基础响应
            $formattedPayload = [
                'code' => $status,
                'message' => $payload['message'],
                'data' => $payload['data'],
            ];

            // 保留其他额外字段（如 pagination）
            foreach ($payload as $key => $value) {
                if (!in_array($key, ['message', 'data'])) {
                    $formattedPayload[$key] = $value;
                }
            }

            $payload = $formattedPayload;
        } else {
            $payload = [
                'code' => $status,
                'message' => $status >= 400 ? ($payload['message'] ?? '请求失败') : '请求成功',
                'data' => $payload,
            ];
        }

        return response()->json($payload, $status, $response->headers->all());
    }
}
