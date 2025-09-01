<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;


class UsersTableSeeder extends Seeder
{
    public function run(): void
    {
        // 出品者A（CO01〜05）
        $sellerA = User::updateOrCreate(
            ['email' => 'seller01@example.com'],
            ['name' => '田中　一郎', 'password' => Hash::make('password123')]
        );

        // 出品者B（CO06〜10）
        $sellerB = User::updateOrCreate(
            ['email' => 'seller02@example.com'],
            ['name' => '山田　花子', 'password' => Hash::make('password456')]
        );

        // 紐づけ無しユーザー
        $neutral = User::updateOrCreate(
            ['email' => 'viewer@example.com'],
            ['name' => '佐藤　次郎', 'password' => Hash::make('password789')]
        );
    }
}
