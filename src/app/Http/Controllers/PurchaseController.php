<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\PurchaseRequest;
use App\Models\Purchase;
use App\Models\PurchaseDetail;
use Illuminate\Support\Facades\Log;


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
}
