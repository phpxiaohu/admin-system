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

# 2. 安装依赖
echo -e "${YELLOW}[步骤] 安装生产依赖...${NC}"
composer install --no-dev --optimize-autoloader
echo -e "${GREEN}[✓] 依赖安装完成${NC}"

# 3. 确保 SQLite 数据库存在
if [ ! -f "database/database.sqlite" ]; then
    echo -e "${YELLOW}[步骤] 创建 SQLite 数据库...${NC}"
    touch database/database.sqlite
    echo -e "${GREEN}[✓] 数据库创建完成${NC}"
fi

# 4. 运行数据库迁移
echo -e "${YELLOW}[步骤] 运行数据库迁移...${NC}"
cp .env.production .env
php artisan migrate --force
echo -e "${GREEN}[✓] 迁移完成${NC}"

# 5. 优化配置
echo -e "${YELLOW}[步骤] 优化配置缓存...${NC}"
php artisan config:cache
php artisan route:cache
echo -e "${GREEN}[✓] 优化完成${NC}"

# 6. 启动本地测试
echo -e "${YELLOW}[步骤] 启动本地测试服务器...${NC}"
echo -e "${GREEN}[提示] 按 Ctrl+C 停止，然后执行 wasmer deploy${NC}"
php artisan serve --host 0.0.0.0 --port 8000
