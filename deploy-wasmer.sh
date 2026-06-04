#!/bin/bash

set -e

GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m'

echo -e "${GREEN}========================================${NC}"
echo -e "${GREEN}   Laravel Wasmer 部署脚本   ${NC}"
echo -e "${GREEN}========================================${NC}"

# 1. 检查 wasmer 是否已安装
if ! command -v wasmer &> /dev/null; then
    echo -e "${RED}[错误] wasmer 未安装${NC}"
    echo -e "${YELLOW}请安装: curl https://get.wasmer.io -sSfL | sh${NC}"
    exit 1
fi

# 2. 检查 PHP 是否已安装
if ! command -v php &> /dev/null; then
    echo -e "${RED}[错误] PHP 未安装${NC}"
    exit 1
fi

# 3. 检查 composer 是否已安装
if ! command -v composer &> /dev/null; then
    echo -e "${RED}[错误] Composer 未安装${NC}"
    exit 1
fi

# 4. 安装依赖
echo -e "${YELLOW}[步骤] 安装生产依赖...${NC}"
composer install --no-dev --optimize-autoloader
echo -e "${GREEN}[✓] 依赖安装完成${NC}"

# 5. 确保 SQLite 数据库存在
if [ ! -f "database/database.sqlite" ]; then
    echo -e "${YELLOW}[步骤] 创建 SQLite 数据库...${NC}"
    touch database/database.sqlite
    echo -e "${GREEN}[✓] 数据库创建完成${NC}"
fi

# 6. 设置环境变量
echo -e "${YELLOW}[步骤] 配置环境变量...${NC}"
cp .env.production .env
echo -e "${GREEN}[✓] 环境变量配置完成${NC}"

# 7. 运行数据库迁移
echo -e "${YELLOW}[步骤] 运行数据库迁移...${NC}"
php artisan migrate --force
echo -e "${GREEN}[✓] 迁移完成${NC}"

# 8. 优化配置
echo -e "${YELLOW}[步骤] 优化配置缓存...${NC}"
php artisan config:cache
php artisan route:cache
echo -e "${GREEN}[✓] 优化完成${NC}"

# 9. 设置目录权限
echo -e "${YELLOW}[步骤] 设置目录权限...${NC}"
chmod -R 777 storage bootstrap/cache
echo -e "${GREEN}[✓] 权限设置完成${NC}"

# 10. 本地测试
echo -e "${YELLOW}[步骤] 启动本地测试服务器...${NC}"
echo -e "${GREEN}[提示] 按 Ctrl+C 停止测试，然后执行 wasmer deploy${NC}"
echo -e "${YELLOW}测试地址: http://localhost:8000${NC}"
php artisan serve --host 0.0.0.0 --port 8000
