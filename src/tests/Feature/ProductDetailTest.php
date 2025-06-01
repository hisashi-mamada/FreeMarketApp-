<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductDetailTest extends TestCase
{
    use RefreshDatabase;

    public function test_商品詳細ページに必要な情報が表示される()
    {
        $user = \App\Models\User::factory()->create();
        $commentUser = \App\Models\User::factory()->create();

        // categories テーブルに手動で挿入（factoryは使わない）
        $category1Id = \DB::table('categories')->insertGetId([
            'name' => 'カテゴリA',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $category2Id = \DB::table('categories')->insertGetId([
            'name' => 'カテゴリB',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $product = \App\Models\Product::factory()->create([
            'name' => 'テスト商品',
            'brand' => 'テストブランド',
            'price' => 1980,
            'description' => 'これは商品の説明です。',
            'image_url' => 'storage/images/sample.jpg',
            'condition' => '新品',
            'category_id' => $category1Id . ',' . $category2Id, // ← 文字列で複数
        ]);

        \DB::table('favorites')->insert([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        \DB::table('comments')->insert([
            'product_id' => $product->id,
            'user_id' => $commentUser->id,
            'body' => 'これはコメントです。',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->get("/item/{$product->id}");

        $response->assertStatus(200);
        $response->assertSee('テスト商品');
        $response->assertSee('テストブランド');
        $response->assertSee('¥1,980');
        $response->assertSee('これは商品の説明です。');
        $response->assertSee('新品');
        $response->assertSee('カテゴリA');
        $response->assertSee('カテゴリB');
        $response->assertSee('これはコメントです。');
        $response->assertSee($commentUser->name);
    }

    public function test_複数選択されたカテゴリが商品詳細ページに表示される()
    {
        // カテゴリを2つ登録
        $category1Id = \DB::table('categories')->insertGetId([
            'name' => 'カテゴリーX',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $category2Id = \DB::table('categories')->insertGetId([
            'name' => 'カテゴリーY',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $product = \App\Models\Product::factory()->create([
            'category_id' => "{$category1Id},{$category2Id}",
        ]);

        $response = $this->get("/item/{$product->id}");

        $response->assertStatus(200);
        $response->assertSee('カテゴリーX');
        $response->assertSee('カテゴリーY');
    }
}
