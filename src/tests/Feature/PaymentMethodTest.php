<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentMethodTest extends TestCase
{
    use RefreshDatabase;

    public function test_支払い方法を選択すると表示に即時反映される()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create([
            'price' => 3000,
            'image_url' => 'sample.jpg',
        ]);

        $response = $this->actingAs($user)->get("/purchase/{$product->id}?payment_method=credit");

        $response->assertStatus(200);
        $response->assertSee('クレジットカード');
    }
}
