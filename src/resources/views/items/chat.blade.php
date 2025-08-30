@extends('layouts.auth')

@section('title', 'チャット画面')

@section('content')
<div class="chatpage">

    {{-- 左：その他の取引（任意・後で動的に差し替え） --}}
    <aside class="chatpage__sidebar">
        <div class="side__title">その他の取引</div>
        <ul class="side__list">
            @foreach($otherChatItems as $item)
            <li>
                <a href="{{ route('items.chat.show', ['product' => $item->id]) }}" class="side__item">
                    {{ $item->name }}
                </a>
            </li>
            @endforeach
        </ul>
    </aside>


    {{-- 右：チャット本体 --}}
    <main class="chatpage__main">

        {{-- ヘッダー --}}
        <header class="chat__header">
            <div class="chat__avatar"></div>

            <h1 class="chat__title">「{{ $partner->name ?? '不明なユーザー' }}」さんとの取引画面</h1>

            {{-- 出品者のみ「取引完了」ボタン表示 --}}
            @if($isSeller && !$isTradeComplete)
            <form method="POST" action="{{ route('chat.complete', ['product' => $product->id]) }}" class="chat__endform">
                @csrf
                <button type="submit" class="btn btn--danger">取引を完了する</button>
            </form>
            @endif
        </header>

        <hr class="chat__divider">


        {{-- 商品サマリー --}}
        <section class="summary">
            <div class="summary__thumb">
                <img src="{{ asset('storage/' . $product->image_url) }}" alt="商品画像" class="summary__ph">
            </div>
            <div class="summary__meta">
                <div class="summary__name">{{ $product->name }}</div>
                <div class="summary__price">¥{{ number_format($product->price) }}</div>
            </div>
        </section>

        {{-- 取引完了メッセージ表示（出品者・購入者両方に） --}}
        @if($isTradeComplete)
        <hr class="chat__divider">
        <div class="trade-complete-msg">
            <p>この取引は完了しています。</p>
            {{-- 購入者のみ評価表示（仮） --}}
            @if($isBuyer)
            <p>今回の取引に対する評価：</p>
            <div class="stars">⭐️⭐️⭐️⭐️⭐️</div>
            @endif
        </div>
        @endif


        <hr class="chat__divider">


        {{-- メッセージリスト（ダミー） --}}
        <section class="thread" aria-live="polite">
            @foreach($messages as $message)
            @php
            $isMe = $message->user_id === Auth::id();
            @endphp

            <article class="msg {{ $isMe ? 'msg--me' : 'msg--other' }}">
                <div class="msg__head">
                    <div class="msg__avatar">
                        <img src="{{ asset('storage/' . ($message->user->profile->image_path ?? 'images/default_user.png')) }}"
                            alt="ユーザー画像"
                            class="chat__avatar__img">
                    </div>

                    <div class="msg__name">{{ $message->user->name }}</div>
                </div>

                <div class="msg__bubble">
                    @if ($message->body)
                    <p class="msg__text">{{ $message->body }}</p>
                    @endif
                    @if ($message->image_path)
                    <img src="{{ asset('storage/' . $message->image_path) }}" alt="添付画像" class="chat-image">
                    @endif
                </div>

                @if($isMe)
                <div class="msg__meta">
                    <span>編集</span><span>削除</span>
                </div>
                @endif
            </article>
            @endforeach
        </section>


        {{-- 入力フォーム（後でPOST先を実ルートに差し替え） --}}
        @if(!$isTradeComplete)
        <section class="composer">
            <form class="composer__form" method="POST" action="{{ route('chat.message.store', ['product' => $product->id]) }}" enctype="multipart/form-data">

                @csrf
                <textarea name="message" class="composer__input" rows="2" placeholder="取引メッセージを記入してください"></textarea>
                <label class="composer__file">
                    画像を追加
                    <input type="file" name="image" accept="image/*" hidden>
                </label>
                <button type="submit" class="composer__sendbtn">
                    <img src="{{ asset('images/inputbuttun 1.png') }}" alt="送信" class="composer__sendicon">
                </button>
            </form>

            {{-- バリデーション表示（後で有効化） --}}
            @error('message')
            <div class="formerror">{{ $message }}</div>
            @enderror
            @error('image')
            <div class="formerror">{{ $message }}</div>
            @enderror
        </section>
        @endif
    </main>
</div>
@endsection