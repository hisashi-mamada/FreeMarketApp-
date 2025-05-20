@extends('layouts.app')

@section('title', '商品購入')

@section('content')


<form method="GET" action="{{ route('purchase.show', ['item_id' => $product->id]) }}">
    <div class="purchase-wrapper">

        <div class="purchase-left">
            <div class="product-summary">
                <img src="{{ asset($product->image_url) }}" alt="商品画像" class="product-image">
                <div class="product-info">
                    <h2>{{ $product->name }}</h2>
                    <p>¥{{ number_format($product->price) }}</p>
                </div>
            </div>

            <hr class="purchase-divider">

            <div class="payment-method">
                <h3>支払い方法</h3>
                <select name="payment_method" onchange="this.form.submit()">
                    <option value="">選択してください</option>
                    <option value="credit" {{ request('payment_method') === 'credit' ? 'selected' : '' }}>クレジットカード</option>
                    <option value="convenience" {{ request('payment_method') === 'convenience' ? 'selected' : '' }}>コンビニ払い</option>
                </select>
            </div>

            <hr class="purchase-divider">

            <div class="shipping-address">
                <div class="address-header">
                    <h3>配送先</h3>
                    <a href="{{ route('purchase.address.edit', ['item_id' => $product->id]) }}">変更する</a>
                </div>
                @if($address)
                <input type="hidden" name="address_id" value="{{ $address->id }}">
                <p>〒 {{ $address->postal_code }}</p>
                <p>{{ $address->prefecture }}{{ $address->city }}{{ $address->block }}{{ $address->building }}</p>
                @else
                <p>配送先住所が登録されていません。配送先を登録してください。</p>
                @endif
            </div>
        </div>

        <hr class="purchase-divider">

        <div class="purchase-right">
            <div class="order-summary">
                <table>
                    <tr>
                        <td class="summary-label">商品代金</td>
                        <td class="summary-value">¥{{ number_format($product->price) }}</td>
                    </tr>
                    <tr>
                        <td class="summary-label">支払い方法</td>
                        <td class="summary-value">
                            @if(request('payment_method') === 'credit')
                            クレジットカード
                            @elseif(request('payment_method') === 'convenience')
                            コンビニ払い
                            @else
                            未選択
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</form>

<form method="POST" action="{{ route('purchase.checkout', ['item_id' => $product->id]) }}">
    @csrf
    <input type="hidden" name="payment_method" value="{{ request('payment_method') }}">
    <input type="hidden" name="address_id" value="{{ $address->id ?? '' }}">
    <button type="submit" class="purchase-btn">購入する</button>
</form>

@endsection
