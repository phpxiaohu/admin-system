<?php

/**
 * Vercel 数据库迁移端点
 * 
 * 访问此文件以在 Vercel 上运行数据库迁移
 * URL: https://your-project.vercel.app/api/migrate
 * 
 * 注意：生产环境中应该保护此端点，例如添加认证中间件
 */

// 设置响应头
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// 处理预检请求
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

try {
    // 加载 Laravel
    require __DIR__ . '/../vendor/autoload.php';
    $app = require_once __DIR__ . '/../bootstrap/app.php';
    
    // 获取 Kernel
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    
    // 运行迁移
    $status = $kernel->call('migrate', [
        '--force' => true,
        '--no-interaction' => true,
    ]);
    
    // 返回结果
    if ($status === 0) {
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Database migrations completed successfully',
            'timestamp' => now()->toISOString(),
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Migration failed with status code: ' . $status,
            'timestamp' => now()->toISOString(),
        ]);
    }
    
} catch (\Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error running migrations',
        'error' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'timestamp' => now()->toISOString(),
    ]);
}
