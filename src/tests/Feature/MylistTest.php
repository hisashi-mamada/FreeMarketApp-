<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MylistTest extends TestCase
{
    use RefreshDatabase;

    public function test_いいねした商品だけが表示される()
    {
        $user = User::factory()->create();

        $productLiked = Product::factory()->create();
        $productNotLiked = Product::factory()->create();

        DB::table('favorites')->insert([
            'user_id' => $user->id,
            'product_id' => $productLiked->id,
        ]);

        $response = $this->actingAs($user)->get('/?tab=mylist');

        $response->assertStatus(200);
        $response->assertSee($productLiked->name);
        $response->assertDontSee($productNotLiked->name);
    }

    public function test_購入済み商品にはSoldラベルが表示される()
    {
        $user = User::factory()->create();

        $purchasedProduct = Product::factory()->create([
            'is_sold' => true,
        ]);

        $notPurchasedProduct = Product::factory()->create([
            'is_sold' => false,
        ]);

        DB::table('favorites')->insert([
            'user_id' => $user->id,
            'product_id' => $purchasedProduct->id,
        ]);
        DB::table('favorites')->insert([
            'user_id' => $user->id,
            'product_id' => $notPurchasedProduct->id,
        ]);

        $response = $this->actingAs($user)->get('/?tab=mylist');

        $response->assertStatus(200);
        $response->assertSee('sold');
        $response->assertSee($purchasedProduct->name);
        $response->assertSee($notPurchasedProduct->name);
    }

    public function test_自分が出品した商品はマイリストに表示されない()
    {
        $user = User::factory()->create();

        $ownProduct = Product::factory()->create([
            'user_id' => $user->id,
            'is_sold'  => false,
        ]);

        $otherUser      = User::factory()->create();
        $otherProduct   = Product::factory()->create([
            'user_id' => $otherUser->id,
            'is_sold' => false,
        ]);

        DB::table('favorites')->insert([
            'user_id'    => $user->id,
            'product_id' => $otherProduct->id,
        ]);

        $response = $this->actingAs($user)->get('/?tab=mylist');
        $response->assertStatus(200);
        $response->assertSee($otherProduct->name);
        $response->assertDontSee($ownProduct->name);
    }

    public function test_未認証の場合はログイン画面にリダイレクトされる()
    {
        $response = $this->get('/?tab=mylist');

        $response->assertRedirect('/login');
    }
}
