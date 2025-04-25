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
            <input type="file" id="image-upload" name="image" class="form-image-input" hidden>
        </div>
</div>



<div class="exhibited-product-detail-area">
    <label class="form-label">カテゴリー</label>
    <div class="category-tags">
        @foreach($categories as $category)
        <label class="category-tag">
            <input type="radio" name="category_id" value="{{ $category->id }}"> {{ $category->name }}
        </label>
        @endforeach
    </div>
</div>

<div class="exhibited-product-detail-area">
    <label class="form-label">商品の状態</label>
    <select name="condition" class="form-select">
        <option value="" disabled selected>選択してください</option>
        <option value="新品">新品</option>
        <option value="未使用に近い">未使用に近い</option>
        <option value="目立った傷なし">目立った傷なし</option>
        <option value="傷や汚れあり">傷や汚れあり</option>
    </select>
</div>

<div class="exhibited-product-detail-area">
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
    <input type="text" name="price" class="form-input">
</div>

<div class="action-bar">
    <button type="submit" class="form-submit-button">出品する</button>
</div>
</form>
</div>
@endsection