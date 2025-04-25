<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;


class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::updateOrCreate(
            ['email' => 'test@example.com'], // 検索条件
            [ // 更新 or 作成内容
                'name' => 'テストユーザー',
                'password' => Hash::make('password123'),
            ]
        );
    }
}
