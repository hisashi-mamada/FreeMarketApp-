@extends('layouts.app')

@section('title', '住所変更')

@section('content')
<div class="auth-content">
    <h1>住所の変更</h1>

    <form method="POST" action="{{ route('purchase.address.update', ['item_id' => $item_id]) }}">

        @csrf
        <label for="postal_code">郵便番号</label>
        <input type="text" id="postal_code" name="postal_code" value="{{ old('postal_code', $address->postal_code ?? '') }}">

        <label for="address">住所</label>
        <input type="text" id="address" name="address" value="{{ old('address', $address->prefecture ?? '') }}">

        <label for="building">建物名</label>
        <input type="text" id="building" name="building" value="{{ old('building', $address->building ?? '') }}">

        <button type="submit" class="register-btn">更新する</button>
    </form>

</div>
@endsection
