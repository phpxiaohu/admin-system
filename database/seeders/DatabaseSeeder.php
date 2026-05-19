<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;
    /**
     * 执行数据库种子填充
     *
     * 创建或更新管理员账号，并调用商品种子文件批量生成测试商品数据。
     * 该方法具有幂等性，可安全地多次执行。
     */
    public function run(): void
    {
        // 创建或更新管理员账号（用户名: admin, 密码: 888888）
        User::query()->updateOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'admin',
                'email' => 'admin@example.com',
                'password' => Hash::make('888888'),
            ]
        );

        // 创建商品数据
        $this->call([
            ProductSeeder::class,
        ]);
    }
}
