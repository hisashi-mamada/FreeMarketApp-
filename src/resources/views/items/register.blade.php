@extends('layouts.auth')

@section('title', '会員登録')

@section('content')
<div class="auth-content">
    <h1>会員登録</h1>

    <form method="POST" action="{{ route('register.post') }}">
        @csrf
        <label for="name">ユーザー名</label>
        <input type="text" id="name" name="name">

        <label for="email">メールアドレス</label>
        <input type="email" id="email" name="email">

        <label for="password">パスワード</label>
        <input type="password" id="password" name="password">

        <label for="password_confirmation">確認用パスワード</label>
        <input type="password" id="password_confirmation" name="password_confirmation">

        <button type="submit" class="register-btn">登録する</button>
    </form>

    <p><a href="{{ route('login') }}" class="login-link">ログインはこちら</a></p>
</div>
@endsection