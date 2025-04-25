@extends('layouts.app')

@section('title', '商品一覧 | COACHTECHフリマ')

@section('content')
<main>
    <div class="toppage-list">
        <a href="{{ url('/') }}" class="{{ request('tab') !== 'mylist' ? 'active' : '' }}">おすすめ</a>
        <a href="{{ url('/?tab=mylist') }}" class="{{ request('tab') === 'mylist' ? 'active' : '' }}">マイリスト</a>
    </div>

    <div class="products-row top-row">
        @foreach ($items as $item)
        <div class="product-box">
            <a href="{{ url('/item/' . $item->id) }}">
                <div class="product-image">
                    <img src="{{ $item->image_url }}" alt="商品画像">
                </div>
                <p class="product-name">{{ $item->name }}</p>
            </a>
        </div>
        @endforeach
    </div>

</main>
@endsection