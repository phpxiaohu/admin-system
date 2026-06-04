#!/bin/bash

# ============================================
# Laravel + Cloudflare Tunnel 一键公网暴露脚本
# 使用前请先安装 cloudflared：
# macOS:  brew install cloudflared
# Linux:  curl -L https://github.com/cloudflare/cloudflared/releases/latest/download/cloudflared-linux-amd64 -o cloudflared && chmod +x cloudflared && sudo mv cloudflared /usr/local/bin/
# ============================================

set -e

# 颜色输出
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

echo -e "${GREEN}========================================${NC}"
echo -e "${GREEN}   Laravel API 一键公网暴露工具   ${NC}"
echo -e "${GREEN}========================================${NC}"
echo ""

# 1. 检查 cloudflared 是否已安装
if ! command -v cloudflared &> /dev/null; then
    echo -e "${RED}[错误] cloudflared 未安装，正在尝试自动安装...${NC}"

    OS=$(uname -s)
    if [ "$OS" = "Darwin" ]; then
        if command -v brew &> /dev/null; then
            brew install cloudflared
        else
            echo -e "${RED}请先安装 Homebrew，然后重新运行本脚本${NC}"
            exit 1
        fi
    elif [ "$OS" = "Linux" ]; then
        curl -L https://github.com/cloudflare/cloudflared/releases/latest/download/cloudflared-linux-amd64 -o /tmp/cloudflared
        chmod +x /tmp/cloudflared
        sudo mv /tmp/cloudflared /usr/local/bin/cloudflared
        echo -e "${GREEN}[成功] cloudflared 安装完成${NC}"
    else
        echo -e "${RED}不支持的操作系统，请手动安装 cloudflared${NC}"
        exit 1
    fi
fi

echo -e "${GREEN}[✓] cloudflared 已就绪${NC}"

# 2. 确保 SQLite 数据库文件存在
if [ ! -f "database/database.sqlite" ]; then
    echo -e "${YELLOW}[提示] SQLite 数据库文件不存在，正在创建...${NC}"
    touch database/database.sqlite
    echo -e "${GREEN}[✓] database/database.sqlite 已创建${NC}"
fi

# 3. 生成 APP_KEY（如果还没有）
if ! grep -q "APP_KEY=base64" .env 2>/dev/null && ! grep -q "APP_KEY=$" .env 2>/dev/null; then
    echo -e "${GREEN}[✓] APP_KEY 已存在${NC}"
else
    echo -e "${YELLOW}[提示] 正在生成 APP_KEY...${NC}"
    php artisan key:generate --force
    echo -e "${GREEN}[✓] APP_KEY 生成完毕${NC}"
fi

# 4. 运行数据库迁移
echo -e "${YELLOW}[提示] 正在运行数据库迁移...${NC}"
php artisan migrate --force 2>/dev/null || echo -e "${YELLOW}[跳过] 无新迁移或迁移失败（数据库可能已是最新）${NC}"

# 5. 启动 Laravel 开发服务器（后台运行）
LARAVEL_PORT=8000
echo ""
echo -e "${GREEN}[启动] 正在启动 Laravel 开发服务器（端口 ${LARAVEL_PORT}）...${NC}"
php artisan serve --host 0.0.0.0 --port ${LARAVEL_PORT} &
LARAVEL_PID=$!
echo -e "${GREEN}[✓] Laravel 服务器已启动（PID: ${LARAVEL_PID}）${NC}"

# 等一秒让服务器完全就绪
sleep 2

# 6. 启动 Cloudflare Tunnel（前台运行，输出公网地址）
echo ""
echo -e "${GREEN}[启动] 正在通过 Cloudflare Tunnel 暴露到公网...${NC}"
echo -e "${YELLOW}========================================${NC}"
echo -e "${YELLOW}  你的公网地址会出现在下方：${NC}"
echo -e "${YELLOW}========================================${NC}"
echo ""

# 捕获退出信号，确保 Ctrl+C 时也能清理
cleanup() {
    echo ""
    echo -e "${YELLOW}[清理] 正在关闭服务...${NC}"
    kill ${LARAVEL_PID} 2>/dev/null
    echo -e "${GREEN}[✓] 所有服务已关闭${NC}"
    exit 0
}
trap cleanup SIGINT SIGTERM

# 启动 tunnel
cloudflared tunnel --url http://localhost:${LARAVEL_PORT} 2>&1 | while IFS= read -r line; do
    # 抓取公网地址
    if echo "$line" | grep -q "trycloudflare.com"; then
        URL=$(echo "$line" | perl -nle 'print $& if m{https://[^\s]+\.trycloudflare\.com}')
        if [ -n "$URL" ]; then
            echo ""
            echo -e "${GREEN}========================================${NC}"
            echo -e "${GREEN}  ✅ 公网访问地址：${NC}"
            echo -e "${GREEN}  ${URL}${NC}"
            echo -e "${GREEN}  ${URL}/api/你的路由${NC}"
            echo -e "${GREEN}========================================${NC}"
            echo ""
            echo -e "${YELLOW}按 Ctrl+C 关闭服务${NC}"
        fi
    fi
done &

TUNNEL_PID=$!

# 等待任意子进程结束
wait ${LARAVEL_PID} ${TUNNEL_PID} 2>/dev/null
