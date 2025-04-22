@extends('layouts.app')

@section('title', '商品購入')

@section('content')
<div class="purchase-wrapper">
    <div class="purchase-left">
        <div class="product-summary">
            <img src="{{ $product->image_url }}" alt="商品画像" class="product-image">
            <div class="product-info">
                <h2>{{ $product->name }}</h2>
                <p>¥{{ number_format($product->price) }}</p>
            </div>
        </div>

        <hr>


        <div class="payment-method">
            <h3>支払い方法</h3>
            <select name="payment_method">
                <option>選択してください</option>
                <option value="credit">クレジットカード</option>
                <option value="convenience">コンビニ払い</option>
            </select>
        </div>

        <hr>


        <div class="shipping-address">
            <div class="address-header">
                <h3>配送先</h3>
                <a href="{{ route('purchase.address.edit', ['item_id' => $product->id]) }}">変更する</a>
            </div>
            <p>〒 XXX-YYYY</p>
            <p>ここには住所と建物が入ります</p>
        </div>


        <hr>

    </div>




    <div class="purchase-right">
        <div class="order-summary">
            <table>
                <tr>
                    <td class="summary-label">商品代金</td>
                    <td class="summary-value">¥{{ number_format($product->price) }}</td>
                </tr>
                <tr>
                    <td class="summary-label">支払い方法</td>
                    <td class="summary-value">コンビニ払い</td>
                </tr>
            </table>
        </div>


        <form method="POST" action="{{ route('purchase.store', ['item_id' => $product->id]) }}">
            @csrf
            <button type="submit" class="purchase-btn">購入する</button>
        </form>
    </div>
</div>
@endsection
