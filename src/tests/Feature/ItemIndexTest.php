<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Product;

class ItemIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_商品ページを開くと全商品が表示される()
    {
        Product::factory()->count(3)->create();

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee(Product::first()->name);
    }

    public function test_購入済み商品には_sold_ラベルが表示される()
    {
        $user = \App\Models\User::factory()->create();

        $product = \App\Models\Product::factory()->create([
            'is_sold' => true,
            'user_id' => null,
            'name' => '購入済み商品',
        ]);

        $response = $this->actingAs($user)->get('/');

        $response->assertSeeText('sold');
        $response->assertSeeText('購入済み商品');
    }

    public function test_自分が出品した商品は一覧に表示されない()
    {
        $user = \App\Models\User::factory()->create();
        $this->actingAs($user);

        $ownProduct = \App\Models\Product::factory()->create([
            'name' => '自分の商品',
            'user_id' => $user->id,
        ]);

        $otherProduct = \App\Models\Product::factory()->create([
            'name' => '他人の商品',
            'user_id' => null,
        ]);

        $response = $this->get('/');

        $response->assertDontSee('自分の商品');

        $response->assertSee('他人の商品');
    }
}
