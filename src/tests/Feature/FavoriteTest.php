<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class FavoriteTest extends TestCase
{
    use RefreshDatabase;

    public function test_いいねすると商品が登録されてカウントが増える()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $this->assertDatabaseMissing('favorites', [
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);

        $response = $this->actingAs($user)->post(route('favorites.toggle', ['product' => $product->id]));

        $response->assertStatus(302);

        $this->assertDatabaseHas('favorites', [
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);

        $response = $this->get("/item/{$product->id}");
        $response->assertSee('1');
    }

    public function test_いいね済みの場合はアイコンが色付き表示になる()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        DB::table('favorites')->insert([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->actingAs($user)->get("/item/{$product->id}");

        $response->assertSee('icon-star-filled.png');
        $response->assertDontSee('icon-star.svg');
    }

    public function test_いいねを再度押すと解除されてカウントが減る()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        DB::table('favorites')->insert([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->actingAs($user)->post(route('favorites.toggle', ['product' => $product->id]));

        $response->assertStatus(302);

        $this->assertDatabaseMissing('favorites', [
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);

        $response = $this->get("/item/{$product->id}");
        $response->assertSee('0');
    }
}
