<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Cart\CartController;
use App\Http\Controllers\Product\ProductController;
use Illuminate\Support\Facades\Route;

// 公开路由
Route::prefix('vite')->group(function (): void {
    // 登录限流：每分钟最多 5 次尝试
    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:login');
    
    // 注册限流：每分钟最多 3 次
    Route::post('/register', [AuthController::class, 'register'])
        ->middleware('throttle:register');

    // 需要认证的路由
    Route::middleware('auth:sanctum')->group(function (): void {
        // 用户信息 - 通用 API 限流
        Route::get('/getUser', [AuthController::class, 'getUser'])
            ->middleware('throttle:api');
        Route::post('/logout', [AuthController::class, 'logout'])
            ->middleware('throttle:api');

        // 商品路由 - 每分钟最多 60 次请求
        Route::get('/products', [ProductController::class, 'index'])
            ->middleware('throttle:products');
        Route::get('/products/{product}', [ProductController::class, 'show'])
            ->middleware('throttle:products');

        // 购物车路由 - 每分钟最多 30 次请求
        Route::prefix('cart')->group(function (): void {
            Route::get('/', [CartController::class, 'index'])
                ->middleware('throttle:cart');           // 获取购物车列表
            Route::post('/', [CartController::class, 'store'])
                ->middleware('throttle:cart');          // 添加到购物车
            Route::put('/{cartItem}', [CartController::class, 'update'])
                ->middleware('throttle:cart'); // 更新购物车商品数量
            Route::delete('/{cartItem}', [CartController::class, 'destroy'])
                ->middleware('throttle:cart'); // 删除购物车商品
            Route::delete('/clear', [CartController::class, 'clear'])
                ->middleware('throttle:cart.clear');   // 清空购物车
        });
    });
});
