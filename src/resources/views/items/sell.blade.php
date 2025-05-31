@extends('layouts.app')

@section('title', '商品の出品')

@section('content')
<div class="sell-container">
    <h1 class="sell-title">商品の出品</h1>

    <form method="POST" action="{{ route('items.store') }}" enctype="multipart/form-data">
        @csrf

        <div class="exhibited-products-image">
            <label class="form-label-image">商品画像</label>
            <label for="image-upload" class="custom-file-label">画像を選択する</label>
            <input type="file" id="image-upload" name="image" class="form-image-input" style="display: none;">
        </div>

        <div class="exhibited-product-detail-area">

            <div class="exhibited-product-product-detail">
                <h2 class="form-section-title">商品の詳細</h2>
            </div>

            <div class="exhibited-product-category-area">
                <h2 class="form-label">カテゴリー</h2>
                <div class="category-tags">
                    @foreach($categories as $category)
                    <label class="category-tag">
                        <input type="checkbox" name="category_ids[]" value="{{ $category->id }}" class="category-checkbox">
                        <span>{{ $category->name }}</span>
                    </label>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="exhibited-product-status">
            <label class="form-label">商品の状態</label>
            <select name="condition" class="form-select status-select">
                <option value="" disabled selected>選択してください</option>
                <option value="新品">新品</option>
                <option value="未使用に近い">未使用に近い</option>
                <option value="目立った傷なし">目立った傷なし</option>
                <option value="傷や汚れあり">傷や汚れあり</option>
            </select>
        </div>

        <div class="exhibited-product-detail-area">
            <h2 class="form-section-title">商品名と説明</h2>
            <label class="form-label">商品名</label>
            <input type="text" name="name" class="form-input">
        </div>

        <div class="exhibited-product-detail-area">
            <label class="form-label">ブランド名</label>
            <input type="text" name="brand" class="form-input">
        </div>

        <div class="exhibited-product-detail-area">
            <label class="form-label">商品の説明</label>
            <textarea name="description" class="form-textarea"></textarea>
        </div>

        <div class="exhibited-product-detail-area">
            <label class="form-label">販売価格</label>
            <div class="price-input-wrapper">
                <span class="yen-mark">¥</span>
                <input type="text" name="price" class="form-input">
            </div>
        </div>

        <div class="action-bar">
            <button type="submit" class="form-submit-button">出品する</button>
        </div>
    </form>
</div>
@endsection