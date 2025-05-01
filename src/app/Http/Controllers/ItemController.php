<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ExhibitionRequest;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->query('tab');
        $keyword = $request->query('keyword');

        if ($tab === 'mylist') {
            $user = Auth::user();
            if (!$user) {
                return redirect('/login');
            }

            $query = $user->favorites()->with('category', 'user');
        } else {
            $query = Product::with('category', 'user');
        }

        if (!empty($keyword)) {
            $query->where('name', 'like', '%' . $keyword . '%');
        }

        $items = $query->latest()->get();

        if ($tab === 'mylist') {
            return view('items.index', compact('items', 'tab'));
        }

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

    public function store(ExhibitionRequest $request)
    {
        $validated = $request->validated();

        $validated['user_id'] = Auth::id();

        Product::create($validated);

        return redirect('/')->with('message', '商品を出品しました！');
    }

    public function addFavorite($product_id)
    {
        $user = Auth::user();
        $user->favorites()->attach($product_id);

        return back()->with('message', 'マイリストに追加しました');
    }


    public function removeFavorite($product_id)
    {
        $user = Auth::user();
        $user->favorites()->detach($product_id);

        return back()->with('message', 'マイリストから削除しました');
    }
}
