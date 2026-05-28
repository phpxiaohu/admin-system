<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 配置限流器
        $this->configureRateLimiting();
    }

    /**
     * 配置 API 限流规则
     */
    protected function configureRateLimiting(): void
    {
        // 登录限流：每分钟最多 5 次尝试（按 IP）
        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });

        // 注册限流：每分钟最多 3 次（按 IP）
        RateLimiter::for('register', function (Request $request) {
            return Limit::perMinute(3)->by($request->ip());
        });

        // 商品查询限流：每分钟最多 60 次（按用户 ID 或 IP）
        RateLimiter::for('products', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        // 购物车操作限流：每分钟最多 30 次（按用户 ID）
        RateLimiter::for('cart', function (Request $request) {
            return Limit::perMinute(30)->by($request->user()?->id ?: $request->ip());
        });

        // 清空购物车限流：每分钟最多 10 次（按用户 ID）
        RateLimiter::for('cart.clear', function (Request $request) {
            return Limit::perMinute(10)->by($request->user()?->id ?: $request->ip());
        });

        // 通用 API 限流：每分钟最多 60 次（按用户 ID 或 IP）
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }
}
