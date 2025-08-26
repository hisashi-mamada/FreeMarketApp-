@extends('layouts.auth')

@section('title', 'チャット画面')

@section('content')
@section('content')
<div class="chatpage">

    {{-- 左：その他の取引（任意・後で動的に差し替え） --}}
    <aside class="chatpage__sidebar">
        <div class="side__title">その他の取引</div>
        <ul class="side__list">
            <li><a href="#" class="side__item">商品名</a></li>
            <li><a href="#" class="side__item">商品名</a></li>
            <li><a href="#" class="side__item">商品名</a></li>
        </ul>
    </aside>

    {{-- 右：チャット本体 --}}
    <main class="chatpage__main">

        {{-- ヘッダー：タイトル＋完了ボタン --}}
        <header class="chat__header">
            <div class="chat__avatar"></div>
            <h1 class="chat__title">「山田太郎」さんとの取引画面</h1>
            <form method="POST" action="#" class="chat__endform">
                @csrf
                <button type="submit" class="btn btn--danger">取引を完了する</button>
            </form>
        </header>

        <hr class="chat__divider">


        {{-- 商品サマリー --}}
        <section class="summary">
            <div class="summary__thumb">
                {{-- 後で storage の画像に差し替え --}}
                <div class="summary__ph">商品画像</div>
            </div>
            <div class="summary__meta">
                <div class="summary__name">商品名</div>
                <div class="summary__price">商品価格</div>
            </div>
        </section>

        <hr class="chat__divider">

        {{-- メッセージリスト（ダミー） --}}
        <section class="thread" aria-live="polite">
            {{-- 相手 --}}
            <div class="msg__head">
                <div class="msg__avatar"></div>
                <div class="msg__name">ユーザー名</div>
            </div>
            <article class="msg msg--other">
                <div class="msg__bubble">
                    <p class="msg__text">こんにちは！商品の状態はどんな感じですか？</p>
                </div>
            </article>

            {{-- 自分 --}}
            <article class="msg msg--me">
                <div class="msg__head">
                    <div class="msg__avatar"></div>
                    <div class="msg__name">ユーザー名</div>
                </div>
                <div class="msg__bubble">
                    <p class="msg__text">ほぼ未使用で目立つ傷はありません！</p>
                </div>
                <div class="msg__meta">
                    <span>編集</span><span>削除</span>
                </div>
            </article>

        </section>

        {{-- 入力フォーム（後でPOST先を実ルートに差し替え） --}}
        <section class="composer">
            <form class="composer__form" method="POST" action="#" enctype="multipart/form-data">
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

    </main>
</div>
@endsection
