<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Cart\CartController;
use App\Http\Controllers\Product\ProductController;
use Illuminate\Support\Facades\Route;

// 公开路由
Route::post('/login', [AuthController::class, 'login']);

// 需要认证的路由
Route::middleware('auth:sanctum')->group(function (): void {
    // 用户信息
    Route::get('/getUser', [AuthController::class, 'getUser']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // 商品路由
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{product}', [ProductController::class, 'show']);

    // 购物车路由
    Route::prefix('cart')->group(function (): void {
        Route::get('/', [CartController::class, 'index']);           // 获取购物车列表
        Route::post('/', [CartController::class, 'store']);          // 添加到购物车
        Route::put('/{cartItem}', [CartController::class, 'update']); // 更新购物车商品数量
        Route::delete('/{cartItem}', [CartController::class, 'destroy']); // 删除购物车商品
        Route::delete('/clear', [CartController::class, 'clear']);   // 清空购物车
    });
});
