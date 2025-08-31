@extends('layouts.app')

@section('title', 'プロフィール')

@section('content')
<div class="mypage-container">


    <div class="user-info">
        <div class="user-image">
            @php
            $userImage = isset($profile) && $profile->image_path
            ? asset('storage/' . $profile->image_path)
            : asset('images/default_user.png');
            @endphp

            <img src="{{ asset('storage/' . optional($profile)->image_path ?? 'images/default_user.png') }}" alt="ユーザー画像">
        </div>

        <div class="user-name">
            <h2>{{ $profile && $profile->nickname ? $profile->nickname : $user->name }}</h2>

            @if(isset($averageRating))
            <div class="average-rating">
                {{ str_repeat('⭐', $averageRating) }}
            </div>
            @endif

        </div>
        <div class="edit-profile">
            <a href="{{ route('profile.edit') }}" class="edit-profile-button">プロフィールを編集</a>
        </div>

    </div>


    <div class="toppage-list">
        <a href="{{ url('/mypage?tab=sell') }}" class="{{ request('tab') !== 'buy' ? 'active' : '' }}">出品した商品</a>
        <a href="{{ url('/mypage?tab=buy') }}" class="{{ request('tab') === 'buy' ? 'active' : '' }}">購入した商品</a>
        <a href="{{ url('/mypage?tab=chat') }}" class="{{ request('tab') === 'chat' ? 'active' : '' }}">取引中の商品</a>

    </div>

    <div class="toppage-list-border"></div>

    <div class="product-grid">
        @php $activeTab = $tab ?? request('tab') ?? 'sell'; @endphp
        @if($activeTab === 'buy')
        @foreach($purchases as $purchase)
        @foreach($purchase->purchaseDetails as $detail)
        <div class="product-card">
            <img src="{{ asset('storage/' . optional($detail->product)->image_url) }}" alt="商品画像">

            <p class="product-name">{{ optional($detail->product)->name }}</p>
        </div>
        @endforeach
        @endforeach

        @elseif($activeTab === 'sell')
        @foreach($products as $product)
        <div class="product-card">
            <img src="{{ asset('storage/' . $product->image_url) }}" alt="商品画像">
            <p class="product-name">{{ $product->name }}</p>
            @php
            $categoryIds = explode(',', $product->category_id);
            @endphp
            <p class="product-category">
                @foreach($categoryIds as $id)
                {{ $allCategories[$id] ?? '不明' }}@if (!$loop->last)、@endif
                @endforeach
            </p>
        </div>
        @endforeach

        @elseif($activeTab === 'chat')
        @foreach($chatItems as $item)
        <a href="{{ route('items.chat.show', ['product' => $item->id]) }}">
            <div class="product-card">
                {{-- 通知マーク（未読メッセージがある場合のみ表示） --}}
                @if(!empty($item->unread_count) && $item->unread_count > 0)
                <div class="notification-badge">{{ $item->unread_count }}</div>
                @endif
                <img src="{{ asset('storage/' . $item->image_url) }}" alt="商品画像">
                <p class="product-name">{{ $item->name }}</p>
            </div>
        </a>

        @endforeach
        @endif


    </div>

</div>
@endsection