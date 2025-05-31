<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductListingTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 出品フォームから商品情報が正しく登録される()
    {
        Storage::fake('public'); // 画像の保存先を偽装

        // ユーザー作成＆ログイン
        $user = User::factory()->create();

        // フォーム入力用のデータ
        $formData = [
            'name' => 'テスト商品',
            'description' => 'これはテスト用の商品説明です。',
            'price' => 5000,
            'condition' => '新品',
            'category_ids' => [1, 2, 3],
            'image' => UploadedFile::fake()->image('dummy.jpg'),
        ];

        $response = $this->actingAs($user)->post(route('items.store'), $formData);

        $response->assertStatus(302); // リダイレクト確認

        $this->assertDatabaseHas('products', [
            'name' => 'テスト商品',
            'description' => 'これはテスト用の商品説明です。',
            'price' => 5000,
            'condition' => '新品',
        ]);
    }
}
