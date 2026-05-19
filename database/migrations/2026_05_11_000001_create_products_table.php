<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 创建商品数据表
     *
     * 定义商品表结构，包含商品名称、描述、价格、库存、图片、上架状态等字段。
     *
     * @return void
     */
    public function up(): void
    {
        // 创建商品表并定义字段结构
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('商品名称');
            $table->string('description')->nullable()->comment('商品描述');
            $table->decimal('price', 10, 2)->comment('商品价格');
            $table->integer('stock')->default(0)->comment('库存数量');
            $table->string('image')->nullable()->comment('商品图片');
            $table->boolean('is_active')->default(true)->comment('是否上架');
            $table->timestamps();
        });
    }

    /**
     * 回滚商品数据表的迁移操作
     *
     * 删除商品数据表，用于迁移回滚操作。
     *
     * @return void
     */
    public function down(): void
    {
        // 删除商品表
        Schema::dropIfExists('products');
    }
};
