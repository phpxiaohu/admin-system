<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 创建购物车项数据表
     * 
     * 定义购物车表结构，包含用户ID、商品ID、数量等字段，
     * 并设置唯一约束确保同一用户的同一商品只有一条记录。
     *
     * @return void
     */
    public function up(): void
    {
        // 创建购物车项表并定义字段结构
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade')->comment('用户ID');
            $table->foreignId('product_id')->constrained()->onDelete('cascade')->comment('商品ID');
            $table->integer('quantity')->default(1)->comment('商品数量');
            $table->timestamps();

            // 确保同一用户的同一商品只有一条记录
            $table->unique(['user_id', 'product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
