@extends('layouts.app')

@section('title', 'プロフィール')

@section('content')
<div class="auth-content">
    <h1>プロフィール設定</h1>

    {{-- ✅ バリデーションエラーメッセージ --}}
    @if ($errors->any())
    <div class="error-message">
        <ul>
            @foreach ($errors->all() as $error)
            <li style="color: red;">{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="profile-user-info">
        <div class="image-button-wrapper">
            <div class="user-image">
                @php
                $userImage = isset($profile) && $profile->image_path
                ? asset('storage/' . $profile->image_path)
                : asset('images/default_user.png');
                @endphp

                <img src="{{ asset('storage/' . $profile->image_path) }}" alt="ユーザー画像">

            </div>

            <div class="profile-image-button" onclick="document.getElementById('image-upload').click();">
                画像を選択する
            </div>
        </div>

        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
            @csrf

            <input type="file" id="image-upload" name="image" style="display: none;">

            <label for="name">ユーザー名</label>
            <input type="text" id="name" name="name"
                value="{{ old('name', $profile->nickname ?? '') }}"
                class="{{ $errors->has('name') ? 'is-error' : '' }}">
            @error('name')
            <div class="error-message">{{ $message }}</div>
            @enderror

            <label for="postal_code">郵便番号</label>
            <input type="text" id="postal_code" name="postal_code"
                placeholder="例: 123-4567"
                value="{{ old('postal_code', $profile->postal_code ?? '') }}">

            <label for="address">住所</label>
            <input type="text" id="address" name="address"
                placeholder="例: 東京都港区1-1-1"
                value="{{ old('address', $profile->address ?? '') }}">

            <label for="building">建物名</label>
            <input type="text" id="building" name="building"
                placeholder="例: ◯◯ビル303"
                value="{{ old('building', $profile->building ?? '') }}">

            <button type="submit" class="register-btn">更新する</button>
        </form>
    </div>
    @endsection