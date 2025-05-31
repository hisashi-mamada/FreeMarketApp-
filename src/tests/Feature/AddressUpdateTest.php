<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use App\Models\Address;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AddressUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_登録した住所が購入画面に反映される()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $this->actingAs($user);

        $address = Address::factory()->create([
            'user_id' => $user->id,
            'postal_code' => '111-1111',
            'prefecture' => '東京都',
            'city' => '渋谷区',
            'block' => '1-1-1',
            'building' => 'ヒカリエ501',
        ]);

        $response = $this->get("/purchase/{$product->id}");

        $response->assertStatus(200);
        $response->assertSee('111-1111');
        $response->assertSee('東京都渋谷区1-1-1ヒカリエ501');
    }

    public function test_購入時に送付先住所が正しく紐づいて登録される()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $this->actingAs($user);

        $address = \App\Models\Address::factory()->create([
            'user_id' => $user->id,
            'postal_code' => '123-4567',
            'prefecture' => '東京都',
            'city' => '新宿区',
            'block' => '西新宿2-8-1',
            'building' => '新宿タワー301'
        ]);

        $response = $this->post("/purchase/{$product->id}", [
            'payment_method' => 'credit',
            'address_id' => $address->id,
        ]);

        $response->assertStatus(302);

        $this->assertDatabaseHas('purchases', [
            'user_id' => $user->id,
            'address_id' => $address->id,
            'payment_method' => 'credit',
        ]);

        $this->assertDatabaseHas('purchase_details', [
            'product_id' => $product->id,
            'quantity' => 1,
        ]);
    }
}
