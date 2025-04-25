<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class PurchaseController extends Controller
{
    public function show($item_id)
    {
        $product = Product::findOrFail($item_id);
        return view('items.purchase', compact('product'));
    }

    public function store(Request $request, $item_id)
    {
        $user = Auth::user();

        return redirect('/')->with('message', '購入が完了しました！');
    }
}
