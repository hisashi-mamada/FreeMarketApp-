@extends('layouts.auth')

@section('title', 'チャット画面')

@section('content')
<div class="chatpage">

    <aside class="chatpage__sidebar">
        <div class="side__title">その他の取引</div>
        <ul class="side__list">
            @if(!$isBuyer)
            @foreach($otherChatItems as $item)
            <li>
                <a href="{{ route('items.chat.show', ['product' => $item->id]) }}" class="side__item">
                    {{ $item->name }}
                </a>
            </li>
            @endforeach
            @endif
        </ul>
    </aside>


    {{-- 右：チャット本体 --}}
    <main class="chatpage__main">

        {{-- ヘッダー --}}
        <header class="chat__header">
            <div class="chat__avatar">
                <img
                    src="{{ asset('storage/' . (optional($partner->profile)->image_path ?? 'images/default_user.png')) }}"
                    alt="{{ $partner->name ?? 'ユーザー' }}"
                    class="chat__avatar__img">
            </div>

            <h1 class="chat__title">「{{ $partner->name ?? '不明なユーザー' }}」さんとの取引画面</h1>

            @if($isBuyer && $detail && $detail->buyer_rating === null)
            <form action="{{ route('items.chat.complete', ['product' => $product->id]) }}"
                method="POST" class="chat__complete">
                @csrf
                <button type="submit" class="btn--danger">取引を完了する</button>
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

        @if($showBuyerModal || $showSellerModal)
        <div class="rating-modal-overlay">
            <div class="rating-modal">
                <h3 class="rating-modal__title">取引が完了しました。</h3>
                <hr class="rating-modal__divider">
                <p class="rating-modal__subtitle">今回の取引相手はどうでしたか？</p>

                <form action="{{ route('rate.store', ['product' => $product->id]) }}" method="POST" class="rating-form">
                    @csrf
                    <div class="rating">
                        <input type="radio" id="star5" name="score" value="5" required>
                        <label class="star" for="star5" title="5">★</label>

                        <input type="radio" id="star4" name="score" value="4">
                        <label class="star" for="star4" title="4">★</label>

                        <input type="radio" id="star3" name="score" value="3">
                        <label class="star" for="star3" title="3">★</label>

                        <input type="radio" id="star2" name="score" value="2">
                        <label class="star" for="star2" title="2">★</label>

                        <input type="radio" id="star1" name="score" value="1">
                        <label class="star" for="star1" title="1">★</label>
                    </div>

                    <hr class="rating-modal__divider">
                    
                    <div class="rating-modal__actions">
                        <button type="submit" class="rating-modal__submit">送信する</button>
                        <a class="rating-modal__close" href="{{ route('items.chat.show', ['product' => $product->id]) }}">閉じる</a>
                    </div>
                </form>
            </div>
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
                    @if(session('editing_comment_id') === $message->id)
                    <form action="{{ route('chat.message.update', ['product' => $product->id, 'comment' => $message->id]) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <input type="text" name="message" value="{{ old('message', $message->body) }}">
                        <input type="file" name="image">
                        <button type="submit">保存</button>
                        <a href="{{ route('items.chat.show', ['product' => $product->id]) }}">キャンセル</a>
                    </form>
                    @else
                    <a href="{{ route('chat.message.edit', ['product' => $product->id, 'comment' => $message->id]) }}">編集</a>
                    <form action="{{ route('chat.message.destroy', ['product' => $product->id, 'comment' => $message->id]) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit">削除</button>
                    </form>
                    @endif

                </div>
                @endif



            </article>
            @endforeach
        </section>


        {{-- 入力フォーム（後でPOST先を実ルートに差し替え） --}}
        @if(!$isTradeComplete)
        <section class="composer">
            {{-- バリデーション表示（後で有効化） --}}
            @error('message')
            <div class="formerror">{{ $message }}</div>
            @enderror
            @error('image')
            <div class="formerror">{{ $message }}</div>
            @enderror

            <form class="composer__form" method="POST" action="{{ route('chat.message.store', ['product' => $product->id]) }}" enctype="multipart/form-data">
                @csrf
                <textarea name="message" class="composer__input" rows="2" placeholder="取引メッセージを記入してください">{{ old('message') }}</textarea>
                <label class="composer__file">
                    画像を追加
                    <input type="file" name="image" accept="image/*" hidden>
                </label>
                <button type="submit" class="composer__sendbtn">
                    <img src="{{ asset('images/inputbuttun 1.png') }}" alt="送信" class="composer__sendicon">
                </button>
            </form>
        </section>
        @endif
    </main>
</div>
@endsection
