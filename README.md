# Laravel 纯后端 API 项目

本项目是一个极简后端模板，仅提供 API 能力，不包含前端页面与前端构建链路。

## 运行环境

- PHP >= 8.3
- Composer >= 2

## 快速启动

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan serve
```

默认服务地址：`http://127.0.0.1:8000`

## 默认测试账号

- 用户名：`xiaohu`
- 密码：`123456`

## 接口列表

### 1) 登录

- 方法：`POST`
- 路径：`/api/login`
- 请求体：

```json
{
  "username": "xiaohu",
  "password": "123456",
  "device_name": "postman"
}
```

### 2) 当前用户信息

- 方法：`GET`
- 路径：`/api/me`
- 认证：`Authorization: Bearer {token}`

### 3) 退出登录

- 方法：`POST`
- 路径：`/api/logout`
- 认证：`Authorization: Bearer {token}`

## 统一返回格式

所有 API 返回以下结构：

```json
{
  "code": 200,
  "message": "请求成功",
  "data": {}
}
```

常见错误：

- 401：未授权访问
- 422：参数验证失败 或 业务校验失败
- 500：服务器内部错误

## 测试

```bash
php artisan test
```
