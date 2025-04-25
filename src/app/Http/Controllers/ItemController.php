<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->query('tab');

        if ($tab === 'mylist') {
            $user = Auth::user();
            if (!$user) {
                return redirect('/login');
            }

            $items = $user->favorites()->with('category', 'user')->latest()->get();
            return view('items.mylist', compact('items', 'tab'));
        }

        $items = Product::with('category', 'user')->latest()->get();
        return view('items.index', compact('items', 'tab'));
    }


    public function show($item_id)
    {
        $product = Product::with('user', 'category')->findOrFail($item_id);
        return view('items.show', compact('product'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('items.sell', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:1',
            'category_id' => 'required|exists:categories,id',
        ]);

        $validated['user_id'] = Auth::id();

        Product::create($validated);

        return redirect('/')->with('message', '商品を出品しました！');
    }
}
