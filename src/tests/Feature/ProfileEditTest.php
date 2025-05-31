<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Profile;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProfileEditTest extends TestCase
{
    use RefreshDatabase;

    public function test_プロフィール編集画面に初期値が表示されている()
    {
        $user = User::factory()->create([
            'name' => 'テスト太郎',
        ]);

        Profile::factory()->create([
            'user_id' => $user->id,
            'nickname' => 'ニックネーム',
            'image_path' => 'profile.jpg',
            'postal_code' => '123-4567',
            'address' => '東京都港区1-1-1',
            'building' => '〇〇ビル303',
            'phone' => '09012345678', // 必須項目がある場合は補完
        ]);

        $response = $this->actingAs($user)->get('/mypage/profile');

        $response->assertStatus(200);
        $response->assertSee('ニックネーム');
        $response->assertSee('123-4567');
        $response->assertSee('東京都港区1-1-1');
        $response->assertSee('〇〇ビル303');
        $response->assertSee('画像を選択する'); // ボタン文言確認
        $response->assertSee('更新する'); // フォーム送信ボタン
    }
}
