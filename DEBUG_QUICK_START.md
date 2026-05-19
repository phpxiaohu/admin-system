# 🚀 PhpStorm 断点调试 - 快速开始

## ✅ 配置已完成!

Xdebug v3.5.1 已成功安装并配置完成。

## 📝 立即开始调试 (3步)

### 第 1 步: 启动调试监听
在 PhpStorm 右上角找到并点击 **电话图标** 📞 
- 图标名称: "Start Listening for PHP Debug Connections"
- 确保图标是高亮/按下状态

### 第 2 步: 设置断点
1. 打开任意 PHP 文件 (例如: `public/test-debug.php`)
2. 在代码行号左侧点击,出现 **红色圆点** 即为断点

### 第 3 步: 启动调试并访问
1. 点击右上角运行配置,选择 **"Laravel Serve (Debug)"**
2. 点击 **绿色虫子图标** 🐛 (或按 `Control + D`)
3. 浏览器访问: `http://localhost:8000/test-debug.php`
4. PhpStorm 会在断点处自动暂停!

## 🎯 调试控制面板

当断点触发时,使用以下快捷键:

| 快捷键 | 功能 | 说明 |
|--------|------|------|
| `F7` | Step Into | 进入函数内部 |
| `F8` | Step Over | 执行下一行(不进入函数) |
| `F9` | Resume | 继续执行到下一个断点 |
| `Shift + F8` | Step Out | 跳出当前函数 |

## 📊 查看变量

断点触发时,底部 Debug 面板显示:
- **Frames**: 调用栈
- **Variables**: 当前所有变量及其值
- **Watches**: 添加要监控的表达式
- **Console**: 可执行 PHP 代码

## 🔧 已创建的配置

### 1. Laravel 服务调试
- 配置文件: `.idea/runConfigurations/Laravel_Serve_Debug.xml`
- 端口: `8000`
- 文档根目录: `public/`

### 2. Artisan 命令调试
- 配置文件: `.idea/runConfigurations/Debug_Artisan.xml`
- 使用方法: 在 Arguments 中输入命令,如 `route:list`

### 3. PHP 服务器配置
- 配置文件: `.idea/php-servers.xml`
- 服务器名: `laravel-local`
- Debugger: Xdebug

### 4. 调试端口配置
- 配置文件: `.idea/php-debug.xml`
- 端口: `9003`
- 接受外部连接: 是

## 🧪 测试调试功能

我们创建了一个测试文件:
```
http://localhost:8000/test-debug.php
```

在这个文件中第 12 行设置断点,然后刷新页面即可测试!

## ⚠️ 常见问题

**Q: 断点不生效?**
A: 确认电话图标 📞 是亮起状态,然后重启 PhpStorm

**Q: 找不到运行配置?**
A: 重启 PhpStorm,配置会自动加载

**Q: 想临时关闭 Xdebug?**
A: 编辑 `/opt/homebrew/etc/php/8.5/php.ini`,将 `xdebug.mode=debug` 改为 `xdebug.mode=off`,然后重启服务器

## 📚 详细文档

查看完整配置文档: `XDEBUG_SETUP.md`

---

**祝你调试愉快!** 🎉
