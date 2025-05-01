<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\PurchaseRequest;
use App\Models\Purchase;

class PurchaseController extends Controller
{
    public function show($item_id)
    {
        $product = Product::findOrFail($item_id);
        return view('items.purchase', compact('product'));
    }

    public function store(PurchaseRequest $request, $item_id)
    {
        $validated = $request->validated();

        $user = Auth::user();

        Purchase::create([
            'user_id' => $user->id,
            'product_id' => $item_id, //
            'address_id' => $validated['address_id'],
            'payment_method' => $validated['payment_method'],
        ]);

        return redirect('/')->with('message', '購入が完了しました！');
    }
}
