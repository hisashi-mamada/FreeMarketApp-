<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_商品名で部分一致検索ができる()
    {
        Product::factory()->create(['name' => 'あかいリンゴ']);
        Product::factory()->create(['name' => 'あおいバナナ']);

        $response = $this->get('/?keyword=リンゴ');

        $response->assertStatus(200);
        $response->assertSee('あかいリンゴ');
        $response->assertDontSee('あおいバナナ');
    }

    public function test_検索状態がマイリストでも保持されている()
    {
        $user = \App\Models\User::factory()->create();

        // 商品データ（「りんご」だけマッチ）
        $productMatch = \App\Models\Product::factory()->create(['name' => 'りんごのジャム']);
        $productIgnore = \App\Models\Product::factory()->create(['name' => 'バナナのケーキ']);

        // 両方とも「いいね」する
        \DB::table('favorites')->insert([
            ['user_id' => $user->id, 'product_id' => $productMatch->id],
            ['user_id' => $user->id, 'product_id' => $productIgnore->id],
        ]);

        // ログインしてマイリストタブに「りんご」キーワード付きでアクセス
        $response = $this->actingAs($user)->get('/?tab=mylist&keyword=りんご');

        $response->assertStatus(200);
        $response->assertSee('りんごのジャム');
        $response->assertDontSee('バナナのケーキ');
    }
}
