<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use App\Models\Profile;
use App\Models\Purchase;
use App\Models\PurchaseDetail;
use App\Models\Address;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_ユーザー情報がマイページに正しく表示される()
    {
        // ユーザーとプロフィール作成
        $user = User::factory()->create();

        Profile::factory()->create([
            'user_id' => $user->id,
            'nickname' => 'ニックネーム',
            'image_path' => 'profile.jpg',
            'phone' => '09012345678', // 必須カラムがある場合は記載
        ]);

        // 出品商品（sellタブで表示）
        Product::factory()->create([
            'user_id' => $user->id,
            'name' => 'テスト商品',
            'category_id' => '1',
            'image_url' => 'images/test.jpg',
        ]);

        // 購入商品（buyタブで表示）
        $product = Product::factory()->create([
            'name' => '購入商品',
            'category_id' => '1',
            'image_url' => 'images/test2.jpg',
        ]);

        $address = Address::factory()->create([
            'user_id' => $user->id,
        ]);

        $purchase = Purchase::factory()->create([
            'user_id' => $user->id,
            'address_id' => $address->id,
            'payment_method' => 'convenience',
        ]);

        PurchaseDetail::factory()->create([
            'purchase_id' => $purchase->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'subtotal' => $product->price,
        ]);

        // 出品商品一覧ページ（tab=sell）確認
        $response = $this->actingAs($user)->get('/mypage?tab=sell');
        $response->assertStatus(200);
        $response->assertSee('ニックネーム');
        $response->assertSee('テスト商品');
        $response->assertSee('プロフィールを編集');

        // 購入商品一覧ページ（tab=buy）確認
        $response = $this->actingAs($user)->get('/mypage?tab=buy');
        $response->assertStatus(200);
        $response->assertSee('購入商品');
    }
}
