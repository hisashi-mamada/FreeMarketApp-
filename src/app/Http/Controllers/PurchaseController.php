<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\PurchaseRequest;
use App\Models\Purchase;
use App\Models\PurchaseDetail;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\Checkout\Session;


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

        $product = Product::findOrFail($item_id);
        $product->is_sold = true;
        $product->save();

        $purchase = Purchase::create([
            'user_id' => $user->id,
            'product_id' => $item_id,
            'address_id' => $validated['address_id'],
            'payment_method' => $validated['payment_method'],
        ]);


        PurchaseDetail::create([
            'purchase_id' => $purchase->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'subtotal' => (float) $product->price,

        ]);

        return redirect('/')->with('message', '購入が完了しました！');
    }

    public function checkout(Request $request, $item_id)
    {
        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

        $product = Product::findOrFail($item_id);

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
            'success_url' => 'http://host.docker.internal/purchase/success',

            'cancel_url' => url('/'),
            'metadata' => [
                'product_id' => $product->id,
                'user_id' => Auth::id(),
            ],
        ]);

        return redirect($session->url);
    }
}
