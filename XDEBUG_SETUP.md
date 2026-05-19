# PhpStorm Xdebug 调试配置完成 ✅

## 已完成的配置

### 1. ✅ Xdebug 已安装
- 版本: Xdebug v3.5.1
- PHP 版本: 8.5.6
- 安装方式: PECL

### 2. ✅ Xdebug 配置已完成
配置文件位置: `/opt/homebrew/etc/php/8.5/php.ini`

配置内容:
```ini
[XDebug]
xdebug.mode=debug
xdebug.start_with_request=yes
xdebug.client_host=127.0.0.1
xdebug.client_port=9003
xdebug.idekey=PHPSTORM
```

### 3. ✅ PhpStorm 运行配置已创建
已创建以下配置文件在 `.idea/runConfigurations/`:
- `Laravel_Serve_Debug.xml` - Laravel 内置服务器调试配置
- `Debug_Artisan.xml` - Artisan 命令调试配置

## 如何在 PhpStorm 中使用断点调试

### 方法一: 调试 Laravel 内置服务器 (推荐)

1. **启动调试监听**
   - 点击 PhpStorm 右上角的电话图标 📞 "Start Listening for PHP Debug Connections"
   - 确保图标是高亮状态

2. **选择运行配置**
   - 点击右上角的运行配置下拉菜单
   - 选择 "Laravel Serve (Debug)"

3. **设置断点**
   - 在代码行号左侧点击,出现红点即为断点

4. **启动调试**
   - 点击绿色的虫子图标 🐛 (Debug)
   - 或使用快捷键 `Control + D` (macOS)

5. **访问应用**
   - 浏览器访问: `http://localhost:8000`
   - 当代码执行到断点时,PhpStorm 会自动暂停

### 方法二: 调试 Artisan 命令

1. **编辑 Artisan 配置**
   - 点击右上角 "Edit Configurations..."
   - 选择 "Debug Artisan"
   - 在 Arguments 中输入要执行的命令,如: `route:list`

2. **设置断点**
   - 在相关代码处设置断点

3. **启动调试**
   - 点击 Debug 按钮运行 Artisan 命令

### 方法三: 使用浏览器扩展 (可选)

如果需要更精细的调试控制:

1. **安装浏览器扩展**
   - Chrome: [Xdebug Helper](https://chrome.google.com/webstore/detail/xdebug-helper/eadndfjplgieldjbigjakmdgkmoaaaoc)
   - Firefox: [Xdebug Helper](https://addons.mozilla.org/en-US/firefox/addon/xdebug-helper-for-firefox/)

2. **配置扩展**
   - 点击扩展图标
   - 选择 "Debug" 模式
   - IDE Key 设置为: `PHPSTORM`

3. **使用流程**
   - 在 PhpStorm 中启动监听 (电话图标)
   - 在浏览器中访问页面
   - 扩展会在请求中自动添加 Xdebug 参数

## 常用调试快捷键 (macOS)

- `F7`: Step Into (进入函数内部)
- `F8`: Step Over (跳过,不进入函数)
- `F9`: Resume Program (继续执行到下一个断点)
- `Shift + F8`: Step Out (跳出当前函数)
- `Control + D`: Debug 当前配置
- `Control + R`: Run 当前配置 (非调试模式)

## 调试面板说明

当断点触发时,PhpStorm 底部会显示 Debug 面板:

- **Frames**: 调用栈信息
- **Variables**: 当前作用域的变量值
- **Watches**: 自定义监控的表达式
- **Console**: 调试控制台,可以执行 PHP 代码

## 验证配置是否成功

运行以下命令验证 Xdebug:
```bash
php -v
```

应该看到:
```
with Xdebug v3.5.1, Copyright (c) 2002-2026, by Derick Rethans
```

检查 Xdebug 配置:
```bash
php -i | grep "xdebug.mode"
```

应该输出:
```
xdebug.mode => debug => debug
```

## 常见问题排查

### 问题 1: 断点不生效
**解决方案:**
1. 确认电话图标 📞 是亮起状态
2. 确认断点是红色实心圆点
3. 检查代码路径是否与项目路径一致
4. 重启 PhpStorm

### 问题 2: 无法连接到调试器
**解决方案:**
1. 检查防火墙是否阻止 9003 端口
2. 确认 xdebug.client_port=9003
3. 在 PhpStorm 中检查: Preferences → Languages & Frameworks → PHP → Debug → Debug port 是否为 9003

### 问题 3: 调试会话立即结束
**解决方案:**
1. 确认 xdebug.start_with_request=yes
2. 检查是否有多个 PHP 版本冲突
3. 运行 `php --ini` 确认加载了正确的 php.ini

### 问题 4: 性能变慢
**解决方案:**
不需要调试时,可以临时关闭 Xdebug:
```bash
# 注释掉 php.ini 中的 xdebug 配置
# 或者修改 xdebug.mode=off
```

然后重启 PHP 服务。

## 下一步

现在你可以:
1. 在任意 PHP 文件中设置断点
2. 启动 Laravel 服务进行调试
3. 查看变量值和调用栈
4. 单步执行代码

祝你调试愉快! 🎉
