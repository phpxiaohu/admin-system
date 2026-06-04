FROM php:8.3-cli

# 安装 SQLite / ZIP / 常用扩展
RUN apt-get update && apt-get install -y \
    libzip-dev unzip curl git sqlite3 libsqlite3-dev \
    && docker-php-ext-install pdo_sqlite bcmath zip \
    && rm -rf /var/lib/apt/lists/*

# 装 Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# 先拷 composer 文件（利用构建缓存）
COPY composer.json composer.lock* ./
RUN composer install --no-dev --optimize-autoloader --no-scripts --no-interaction

# 拷全部代码
COPY . .

# storage / cache 权限
RUN chmod -R 755 storage bootstrap/cache \
    && chmod +x artisan

# 确保 SQLite 文件存在
RUN touch /var/www/html/database/database.sqlite \
    && chmod 666 /var/www/html/database/database.sqlite

# 运行 migrate + 生成 key（非交互式）
RUN php artisan key:generate --no-interaction 2>/dev/null || true

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8080"]
