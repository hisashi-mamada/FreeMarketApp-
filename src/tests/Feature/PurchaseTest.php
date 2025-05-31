<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PurchaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_購入ボタンを押すと購入が完了する()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['is_sold' => false]);

        $address = \App\Models\Address::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->post(route('purchase.store', ['item_id' => $product->id]), [
            'payment_method' => 'credit',
            'address_id' => $address->id,
        ]);

        $response->assertStatus(302);
        $this->assertTrue(Product::find($product->id)->is_sold == true);
    }

    public function test_購入した商品は一覧画面でsoldと表示される()
    {
        $user = \App\Models\User::factory()->create();

        $product = \App\Models\Product::factory()->create([
            'is_sold' => true,
        ]);

        $response = $this->actingAs($user)->get('/');

        $response->assertStatus(200);
        $response->assertSee($product->name);
        $response->assertSee('sold');
    }
    public function test_購入した商品がプロフィールの購入履歴に表示される()
    {
        $user = \App\Models\User::factory()->create();
        $product = \App\Models\Product::factory()->create();

        $address = \App\Models\Address::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->actingAs($user)->post(route('purchase.store', ['item_id' => $product->id]), [
            'payment_method' => 'credit',
            'address_id' => $address->id,
        ]);

        $response = $this->actingAs($user)->get('/mypage?tab=buy');
        $response->assertStatus(200);
        $response->assertSee($product->name);
    }
}
