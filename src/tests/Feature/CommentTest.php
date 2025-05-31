<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    public function test_ログイン済みユーザーはコメントを送信できる()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $response = $this->actingAs($user)->post(route('comment.add', ['product' => $product->id]), [
            'comment' => 'これはテストコメントです。',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect("/item/{$product->id}");

        $this->assertDatabaseHas('comments', [
            'product_id' => $product->id,
            'user_id' => $user->id,
            'body' => 'これはテストコメントです。',
        ]);

        $response = $this->get("/item/{$product->id}");
        $response->assertSee('これはテストコメントです。');
    }

    public function test_未ログインユーザーはコメントを送信できない()
    {
        $product = \App\Models\Product::factory()->create();

        $response = $this->post(route('comment.add', ['product' => $product->id]), [
            'comment' => '未ログインのテストコメント',
        ]);

        $response->assertRedirect(route('login'));

        $this->assertDatabaseMissing('comments', [
            'product_id' => $product->id,
            'body' => '未ログインのテストコメント',
        ]);
    }

    public function test_コメントが未入力の場合バリデーションエラーになる()
    {
        $user = \App\Models\User::factory()->create();
        $product = \App\Models\Product::factory()->create();

        $response = $this->actingAs($user)->from("/item/{$product->id}")
            ->post(route('comment.add', ['product' => $product->id]), [
                'comment' => '',
            ]);


        $response->assertRedirect("/item/{$product->id}");
        $response->assertSessionHasErrors('comment');

        $this->assertDatabaseMissing('comments', [
            'product_id' => $product->id,
            'user_id' => $user->id,
        ]);
    }

    public function test_コメントが256文字以上の場合バリデーションエラーになる()
    {
        $user = \App\Models\User::factory()->create();
        $product = \App\Models\Product::factory()->create();

        $longComment = str_repeat('あ', 256);

        $response = $this->actingAs($user)->from("/item/{$product->id}")
            ->post(route('comment.add', ['product' => $product->id]), [
                'comment' => $longComment,
            ]);


        $response->assertRedirect("/item/{$product->id}");
        $response->assertSessionHasErrors('comment');

        $this->assertDatabaseMissing('comments', [
            'product_id' => $product->id,
            'user_id' => $user->id,
            'body' => $longComment,
        ]);
    }
}
