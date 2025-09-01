<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\PurchaseRequest;
use App\Models\Purchase;
use App\Models\PurchaseDetail;
use Stripe\Stripe;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    public function show($item_id)
    {
        $product = Product::findOrFail($item_id);
        $address = Auth::user()->addresses()->latest()->first();


        return view('items.purchase', compact('product', 'address'));
    }

    public function store(PurchaseRequest $request, $item_id)
    {
        $validated = $request->validated();
        $user = Auth::user();
        DB::transaction(function () use ($validated, $user, $item_id) {
            // 商品取得＆売却済みに
            $product = Product::findOrFail($item_id);
            $product->is_sold = true;
            $product->save();

            // purchases（new→save で確実に作成）
            $purchase = new Purchase;
            $purchase->user_id        = $user->id;
            $purchase->address_id     = $validated['address_id'];
            $purchase->payment_method = $validated['payment_method'];
            $purchase->save();

            // purchase_details（new→save）
            $detail = new PurchaseDetail;
            $detail->purchase_id = $purchase->id;
            $detail->product_id  = $product->id;
            $detail->quantity    = 1;
            $detail->subtotal    = (float) $product->price;
            $detail->save();
        });

        return redirect('/')->with('message', '購入が完了しました！');
    }

    public function checkout(Request $request, $item_id)
    {
        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

        $product = Product::findOrFail($item_id);

        $request->validate([
            'address_id' => 'required|exists:addresses,id',
            'payment_method' => 'required|string',
        ]);

        $session = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'jpy',
                    'product_data' => [
                        'name' => $product->name,
                    ],
                    'unit_amount' => (int) $product->price,

                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => route('purchase.success') . '?session_id={CHECKOUT_SESSION_ID}',

            'cancel_url' => url('/'),
            'metadata' => [
                'product_id' => $product->id,
                'user_id' => Auth::id(),
                'address_id' => (int)$request->input('address_id'),
                'payment_method' => (string)$request->input('payment_method'),
            ],
        ]);

        return redirect($session->url);
    }

    public function success(Request $request)
    {
        // Stripeのシークレットキー
        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

        $sessionId = $request->query('session_id');
        if (!$sessionId) {
            return redirect('/')->with('error', '決済セッションが見つかりません。');
        }

        // セッション取得（支払い確認）
        try {
            $session = \Stripe\Checkout\Session::retrieve($sessionId);
        } catch (\Exception $e) {
            return redirect('/')->with('error', '決済情報の取得に失敗しました。');
        }

        if ($session->payment_status !== 'paid') {
            return redirect('/')->with('error', '支払いが確認できませんでした。');
        }

        // メタデータから購入情報を取得
        $meta       = $session->metadata ?: new \stdClass();
        $productId  = isset($meta->product_id) ? (int) $meta->product_id : null;
        $buyerId    = isset($meta->user_id) ? (int) $meta->user_id : null;
        $addressId  = isset($meta->address_id) ? (int) $meta->address_id : null;
        $payMethod  = isset($meta->payment_method) ? (string) $meta->payment_method : 'card';

        if (!$productId || !$buyerId) {
            return redirect('/')->with('error', '購入情報が不完全です。');
        }

        // 念のため：ログイン中ユーザーと一致チェック
        if (Auth::id() !== $buyerId) {
            return redirect('/')->with('error', '購入者とログインユーザーが一致しません。');
        }

        // 二重作成防止 & 登録
        DB::transaction(function () use ($productId, $buyerId, $addressId, $payMethod) {
            $already = PurchaseDetail::where('product_id', $productId)
                ->whereHas('purchase', function ($q) use ($buyerId) {
                    $q->where('user_id', $buyerId);
                })
                ->exists();

            if ($already) {
                return;
            }

            $product = Product::findOrFail($productId);

            // purchases
            $purchase = new Purchase;
            $purchase->user_id        = $buyerId;
            $purchase->address_id     = $addressId;
            $purchase->payment_method = $payMethod;
            $purchase->save();

            // purchase_details
            $detail = new PurchaseDetail;
            $detail->purchase_id = $purchase->id;
            $detail->product_id  = $product->id;
            $detail->quantity    = 1;
            if (Schema::hasColumn('purchase_details', 'price_at_time')) {
                $detail->price_at_time = (float) $product->price;
            } elseif (Schema::hasColumn('purchase_details', 'subtotal')) {
                $detail->subtotal = (float) $product->price;
            }
            $detail->save();

            // 商品を売却済みに
            $product->is_sold = true;
            $product->save();
        });

        // マイページ（購入タブ）へ
        return redirect()->route('mypage.index', ['tab' => 'buy'])
            ->with('status', '購入が完了しました。');
    }
}
