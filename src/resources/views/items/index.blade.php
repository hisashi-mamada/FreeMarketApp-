@extends('layouts.app')

@section('title', '商品一覧 | COACHTECHフリマ')

@section('content')
<main>
    <div class="toppage-list">
        <a href="{{ url('/') }}" class="{{ request('tab') !== 'mylist' ? 'active' : '' }}">おすすめ</a>
        <a href="{{ url('/?tab=mylist&keyword=' . request('keyword')) }}"
            class="{{ request('tab') === 'mylist' ? 'active' : '' }}">マイリスト</a>
    </div>

    <div class="toppage-list-border"></div>

    <div class="products-row top-row">
        @if (request('tab') === 'mylist')

        @forelse ($items as $item)
        <div class="product-box">
            <a href="{{ url('/item/' . $item->id) }}">
                <div class="product-image">
                    <img src="{{ asset('storage/' . ltrim($item->image_url, '/')) }}" alt="商品画像">
                    @auth
                    @if ($item->isSold())
                    <span class="sold-label">sold</span>
                    @endif
                    @endauth

                </div>
                <p class="product-name">{{ $item->name }}</p>
            </a>

            @auth
            <form method="POST" action="{{ route('favorites.remove', $item->id) }}">
                @csrf
                <button type="submit" class="favorite-button">❤️ 登録解除</button>
            </form>
            @endauth
        </div>
        @empty
        <p>マイリストに商品がありません。</p>
        @endforelse

        @else

        @foreach ($items as $item)
        <div class="product-box">
            <a href="{{ url('/item/' . $item->id) }}">
                <div class="product-image">
                    <img src="{{ asset('storage/' . $item->image_url) }}" alt="商品画像">

                    @auth
                    @if ($item->isSold())
                    <span class="sold-label">sold</span>
                    @endif
                    @endauth
                </div>
                <p class="product-name">{{ $item->name }}</p>
            </a>

            @auth
            <form method="POST" action="{{ route($item->isFavoritedBy(Auth::user()) ? 'favorites.remove' : 'favorites.add', $item->id) }}">
                @csrf
                <button type="submit" class="favorite-button">
                    @if ($item->isFavoritedBy(Auth::user()))
                    ❤️ 登録解除
                    @else
                    🤍 マイリストに追加
                    @endif
                </button>
            </form>
            @endauth
        </div>
        @endforeach
        @endif
    </div>
</main>
@endsection