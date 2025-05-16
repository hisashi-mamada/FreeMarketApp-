<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $products = [
            [
                'name' => '腕時計',
                'price' => 15000,
                'description' => 'スタイリッシュなデザインのメンズ腕時計',
                'image_url' => 'storage/images/product_01_watch.jpg',
                'condition' => '良好',
                'category_id' => '1,5,12',
                'brand_name' => 'アルマーニ',
                'user_id' => null,
            ],
            [
                'name' => 'HDD',
                'price' => 5000,
                'description' => '高速で信頼性の高いハードディスク',
                'image_url' => 'storage/images/product_02_hdd.jpg',
                'condition' => '目立った傷や汚れなし',
                'category_id' => '2,3',
                'brand_name' => 'HDD',
                'user_id' => null,
            ],
            [
                'name' => '玉ねぎ3束',
                'price' => 300,
                'description' => '新鮮たまねぎ3束のセット',
                'image_url' => 'storage/images/product_03_onion.jpg',
                'condition' => 'やや傷や汚れあり',
                'category_id' => '4,10',
                'brand_name' => 'アイラブイメージ',
                'user_id' => null,
            ],
            [
                'name' => '革靴',
                'price' => 4000,
                'description' => 'クラシックなデザインの革靴',
                'image_url' => 'storage/images/product_04_shoes.jpg',
                'condition' => '状態が悪い',
                'category_id' => '1,5,6,12',
                'brand_name' => 'レザーシューズ',
                'user_id' => null,
            ],
            [
                'name' => 'ノートPC',
                'price' => 45000,
                'description' => '高性能なノートパソコン',
                'image_url' => 'storage/images/product_05_notepc.jpg',
                'condition' => '良好',
                'category_id' => '2,4,5',
                'brand_name' => 'ラップトップ',
                'user_id' => null,
            ],
            [
                'name' => 'マイク',
                'price' => 8000,
                'description' => '高音質のレコーディング用マイク',
                'image_url' => 'storage/images/product_06_mic.jpg',
                'condition' => '目立った傷や汚れなし',
                'category_id' => '2,8,13,14',
                'brand_name' => 'ミュージックマイク',
                'user_id' => null,
            ],
            [
                'name' => 'ショルダーバッグ',
                'price' => 3500,
                'description' => 'おしゃれなショルダーバッグ',
                'image_url' => 'storage/images/product_07_bag.jpg',
                'condition' => 'やや傷や汚れあり',
                'category_id' => '1,4,6,12',
                'brand_name' => 'ヤスイバックヤスイバック',
                'user_id' => null,
            ],
            [
                'name' => 'タンブラー',
                'price' => 500,
                'description' => '使いやすいタンブラー',
                'image_url' => 'storage/images/product_08_tumbler.jpg',
                'condition' => '状態が悪い',
                'category_id' => '3,4,5,6,10',
                'brand_name' => 'スーベニア',
                'user_id' => null,
            ],
            [
                'name' => 'コーヒーミル',
                'price' => 2000,
                'description' => '手動のコーヒーミル',
                'image_url' => 'storage/images/product_09_coffee.jpg',
                'condition' => '良好',
                'category_id' => '3,10',
                'brand_name' => 'グリンダ―',
                'user_id' => null,
            ],
            [
                'name' => 'メイクセット',
                'price' => 2500,
                'description' => '便利なメイクアップセット',
                'image_url' => 'storage/images/product_10_make.jpg',
                'condition' => '目立った傷や汚れなし',
                'category_id' => '1,4,6,11,12',
                'brand_name' => '女子力',
                'user_id' => null,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
