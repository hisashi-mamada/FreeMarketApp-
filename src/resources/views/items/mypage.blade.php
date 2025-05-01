@extends('layouts.app')

@section('title', 'プロフィール')

@section('content')
<div class="mypage-container">

    <div class="user-info">
        <div class="user-image">
            <img src="{{ asset('images/default_user.png') }}" alt="ユーザー画像">
        </div>
        <div class="user-name">
            <h2>ユーザー名</h2>
        </div>
        <div class="edit-profile">
            <a href="{{ route('profile.edit') }}" class="edit-profile-button">プロフィールを編集</a>
        </div>
    </div>


    <div class="toppage-list">
        <a href="{{ url('/mypage?tab=sell') }}" class="{{ request('tab') !== 'buy' ? 'active' : '' }}">出品した商品</a>
        <a href="{{ url('/mypage?tab=buy') }}" class="{{ request('tab') === 'buy' ? 'active' : '' }}">購入した商品</a>
    </div>

    <div class="toppage-list-border"></div>

    <div class="product-grid">
        @if($tab === 'buy')
        @foreach($purchases as $purchase)
        @foreach($purchase->purchaseDetails as $detail)
        <div class="product-card">
            <img src="{{ asset('storage/' . $detail->product->image_path) }}" alt="商品画像">
            <p class="product-name">{{ $detail->product->name }}</p>
        </div>
        @endforeach
        @endforeach
        @else
        @foreach($products as $product)
        <div class="product-card">
            <img src="{{ asset('storage/' . $product->image_path) }}" alt="商品画像">
            <p class="product-name">{{ $product->name }}</p>
        </div>
        @endforeach
        @endif
    </div>

</div>
@endsection