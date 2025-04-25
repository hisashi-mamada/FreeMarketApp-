@extends('layouts.app')

@section('content')
<div class="product-detail">
    <div class="product-image-area">
        <div class="product-image">
            <img src="{{ $product->image_url }}" alt="商品画像">
        </div>
    </div>

    <div class="product-description-area">
        <div class="product-title">
            <h1>{{ $product->name }}</h1>
            <p class="brand-name">ブランド名</p>
            <p class="price">¥{{ number_format($product->price) }}（税込）</p>

            <div class="product-actions">
                <img src="{{ asset('images/icon-star.svg') }}" alt="お気に入り">
                <span>3</span>
                <img src="{{ asset('images/icon-commet.svg') }}" alt="コメント">
                <span>1</span>
            </div>
        </div>

        <div class="purchase-area">
            {{-- お気に入り数や購入ボタンなどを入れる予定ならここ --}}
            <a href="{{ route('purchase.show', ['item_id' => $product->id]) }}" class="purchase-button">購入手続きへ</a>
        </div>


        <div class="product-description">
            <h2>商品説明</h2>
            <p>{{ $product->description }}</p>
        </div>

        <div class="product-info">
            <h2>商品の情報</h2>
            <p>カテゴリ：{{ $product->category->name }}</p>
            <p>商品の状態：{{ $product->condition }}</p>
        </div>

        <div class="product-comments">
            <h2>コメント</h2>
            <p>出品者: {{ $product->user->name }}</p>
            {{-- コメント機能追加予定ならここ --}}
        </div>
    </div>
</div>
@endsection