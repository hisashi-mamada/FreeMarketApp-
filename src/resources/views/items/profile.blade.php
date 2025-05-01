@extends('layouts.app')

@section('title', 'プロフィール')

@section('content')
<div class="auth-content">
    <h1>プロフィール設定</h1>

    <div class="profile-user-info">
        <div class="user-image">
            <img src="{{ asset('images/default_user.png') }}" alt="ユーザー画像">
        </div>

        <div class="profile-image-button" onclick="document.getElementById('image-upload').click();">
            画像を選択する
        </div>
    </div>


    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
        @csrf

        <label for="name">ユーザー名</label>
        <input type="text" id="name" name="name" value="{{ old('name') }}" class="{{ $errors->has('name') ? 'is-error' : '' }}">
        @error('name')
        <div class="error-message">{{ $message }}</div>
        @enderror

        <label for="postal_code">郵便番号</label>
        <input type="text" id="postal_code" name="postal_code">

        <label for="address">住所</label>
        <input type="text" id="address" name="address">

        <label for="building">建物名</label>
        <input type="text" id="building" name="building">

        <button type="submit" class="register-btn">更新する</button>
    </form>

</div>
@endsection
