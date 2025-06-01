@extends('layouts.app')

@section('content')
<div class="product-detail">
    <div class="product-image-area">
        <div class="product-detail-image">
            <img src="{{ asset($product->image_url) }}" alt="商品画像">

        </div>
    </div>

    <div class="product-description-area">
        <div class="product-title">
            <h1>{{ $product->name }}</h1>
            <p class="brand-name">{{ $product->brand ?? 'ブランド不明' }}</p>

            <p class="price">¥{{ number_format($product->price) }}（税込）</p>

            <div class="product-actions">
                @auth
                <form action="{{ route('favorites.toggle', ['product' => $product->id]) }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" style="background: none; border: none; cursor: pointer;">
                        @if ($product->isFavoritedBy(auth()->user()))
                        <img src="{{ asset('images/icon-star-filled.png') }}" alt="いいね">
                        @else
                        <img src="{{ asset('images/icon-star.svg') }}" alt="いいねしてない">
                        @endif
                    </button>
                </form>
                @else
                <img src="{{ asset('images/icon-star.svg') }}" alt="お気に入り">
                @endauth

                <span>{{ $product->favorited_users_count ?? 0 }}</span>

                <img src="{{ asset('images/icon-commet.svg') }}" alt="コメント">
                <span>{{ $product->comments->count() }}</span>
            </div>
        </div>

        <div class="purchase-area">
            <a href="{{ route('purchase.show', ['item_id' => $product->id]) }}" class="purchase-button">購入手続きへ</a>
        </div>

        <div class="product-description">
            <h2>商品説明</h2>
            <p>{{ $product->description }}</p>
        </div>

        <div class="product-info">
            <h2 class="product-info-title">商品の情報</h2>
            <div class="category-info">
                <span class="category-label">カテゴリー：</span>
                @php
                $categoryIds = explode(',', $product->category_id);
                $categories = \App\Models\Category::whereIn('id', $categoryIds)->get();
                @endphp

                @foreach ($categories as $category)
                <span class="category-tag">{{ $category->name }}</span>
                @endforeach
            </div>


            <div class="product-condition">
                <span class="condition-label">商品の状態：</span>
                <span class="condition-text">{{ $product->condition }}</span>
            </div>
        </div>

        <div class="product-comments">
            <h2>コメント({{ $product->comments->count() }})</h2>

            @foreach ($product->comments as $comment)
            <div class="comments-list">
                <div class="comment-user-info">
                    @php
                    $userImage = isset($comment->user->profile) && $comment->user->profile->image_path
                    ? asset('storage/' . $comment->user->profile->image_path)
                    : asset('images/default_user.png');
                    @endphp

                    <img src="{{ $userImage }}" alt="ユーザー画像" class="comment-avatar">
                    <span class="user-name">{{ $comment->user->name }}</span>
                </div>
                <div class="comment-text">{{ $comment->body }}</div>
            </div>
            @endforeach

            <div class="comment-input">
                <h3>商品へのコメント</h3>
                <form action="{{ route('comment.add', ['product' => $product->id]) }}" method="POST" class="comments-form">
                    @csrf
                    <textarea name="comment" rows="4" placeholder="コメントを入力してください" {{ auth()->check() ? '' : 'disabled' }}></textarea>
                    @error('comment')
                    <p style="color: red;">{{ $message }}</p>
                    @enderror

                    @auth
                    <button type="submit" class="purchase-button">
                        <span class="comment-button-text">コメントを送信する</span>
                    </button>
                    @else
                    <a href="{{ route('login') }}" class="purchase-button" style="display: inline-block; text-align: center;">
                        <span class="comment-button-text">コメントを送信する</span>
                    </a>
                    @endauth
                </form>

            </div>
        </div>
    </div>
</div>
@endsection