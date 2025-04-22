@extends('layouts.auth')

@section('title', 'ログイン')

@section('content')
<div class="auth-content">
    <h1>ログイン</h1>

    <form method="POST" action="{{ route('login') }}">
        @csrf
        <label for="email">メールアドレス</label>
        <input type="email" id="email" name="email">

        <label for="password">パスワード</label>
        <input type="password" id="password" name="password">

        <button type="submit" class="register-btn">ログインする</button>
    </form>

    <p><a href="{{ route('register.show') }}" class="login-link">会員登録はこちら</a></p>
</div>
@endsection