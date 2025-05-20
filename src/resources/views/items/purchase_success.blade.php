@extends('layouts.auth')

@section('title', '購入完了')

@section('content')
<div class="success-message">
    <h1>購入が完了しました！</h1>
    <p>ご購入ありがとうございます。商品は近日中に発送されます。</p>
    <a href="{{ url('/') }}">商品一覧画面へ</a>
</div>
@endsection