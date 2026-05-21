<?php

return [
    /*
    跨源资源共享（CORS）配置
    */
    // 允许的请求路径（* 匹配所有路由）
    'paths' => ['*'],
    // 允许的请求方法
    'allowed_methods' => ['*'],
    // 允许的域名（* 允许所有域名）
    'allowed_origins' => ['*'],
    // 允许的域名（* 允许所有域名）
    'allowed_origins_patterns' => [],
    // 允许的请求头
    'allowed_headers' => ['*'],
    // 暴露的请求头
    'exposed_headers' => [],
    // 缓存时间
    'max_age' => 0,
    // 允许携带cookie
    'supports_credentials' => false,

];
