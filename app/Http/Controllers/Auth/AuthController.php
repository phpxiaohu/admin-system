<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Requests\Api\RegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // 登录
    public function login(LoginRequest $request): JsonResponse
    {
        $data = $request->validated();

        $user = User::query()->where('username', $data['username'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            return response()->json([
                'message' => '用户名或密码错误',
            ], 422);
        }

        $token = $user->createToken($data['device_name'] ?? 'web')->plainTextToken;

        // 计算过期时间（2小时后）
        $expiresAt = now()->addMinutes(config('sanctum.expiration'));

        return response()->json([
            'message' => '登录成功',
            'data' => [
                'accessToken' => $token,
                'expiresAt' => $expiresAt->timestamp,
                'user' => $user,
            ],
        ]);
    }
    //  注册
    public function register(RegisterRequest $request): JsonResponse
    {
        $data = $request->validated();

        $user = User::create([
            'name' => $data['username'],
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $token = $user->createToken($data['device_name'] ?? 'web')->plainTextToken;

        // 计算过期时间（2小时后）
        $expiresAt = now()->addMinutes(config('sanctum.expiration'));

        return response()->json([
            'message' => '注册成功',
            'data' => [
                'accessToken' => $token,
                'expiresAt' => $expiresAt->timestamp,
                'user' => $user,
            ],
        ]);
    }

    public function getUser(Request $request): JsonResponse
    {
        return response()->json([
            'message' => '获取成功',
            'data' => $request->user(),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()?->currentAccessToken()?->delete();

        return response()->json([
            'message' => '退出成功',
        ]);
    }
}
