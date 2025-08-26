<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
use App\Models\Comment;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ExhibitionRequest;
use App\Http\Requests\CommentRequest;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->query('tab');
        $keyword = $request->query('keyword');
        $query = null; // ← 先に宣言しておくことで安全

        if ($tab === 'mylist') {
            $user = Auth::user();
            if (!$user) {
                return redirect('/login');
            }

            $query = $user->favorites()->with('category', 'user');
        } else {
            $query = Product::with('category', 'user');

            // 🔧 user_id = null or user_id != 自分 → 表示対象とする
            if (Auth::check()) {
                $query->where(function ($q) {
                    $q->whereNull('user_id')
                        ->orWhere('user_id', '!=', Auth::id());
                });
            }

            if (!empty($keyword)) {
                $query->where('name', 'like', '%' . $keyword . '%');
            }
        }

        // 念のため null チェック
        if (!$query) {
            $query = Product::with('category', 'user');
        }

        $items = $query->orderBy('id')->get();

        return view('items.index', compact('items', 'tab'));
    }



    public function show($item_id)
    {
        $product = Product::with('user', 'category', 'comments', 'favoritedUsers')
            ->withCount('favoritedUsers')
            ->findOrFail($item_id);

        return view('items.show', compact('product'));
    }


    public function create()
    {
        $categories = Category::all();
        return view('items.sell', compact('categories'));
    }

    public function store(ExhibitionRequest $request)
    {
        dd(Auth::id());

        $validated = $request->validated();

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('product_images', 'public');
        }

        $validated['category_id'] = isset($validated['category_ids'])
            ? implode(',', $validated['category_ids'])
            : null;

        unset($validated['category_ids']);

        Product::create([
            'user_id'     => Auth::id(),
            'name'        => $validated['name'],
            'description' => $validated['description'],
            'price'       => $validated['price'],
            'condition'   => $validated['condition'],
            'image_url'   => $imagePath,
            'is_sold'     => false,
            'category_id' => $validated['category_id'],
            'brand' => $validated['brand'] ?? null,

        ]);


        return redirect('/mypage?tab=sell')->with('message', '商品を出品しました！');
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

    public function toggleFavorite(Product $product)
    {
        $user = auth()->user();

        if ($product->isFavoritedBy($user)) {
            $product->favoritedUsers()->detach($user->id);
        } else {
            $product->favoritedUsers()->attach($user->id);
        }

        return redirect()->route('items.show', ['item_id' => $product->id]);
    }

    public function addComment(CommentRequest $request, $product_id)
    {
        Comment::create([
            'user_id' => Auth::id(),
            'product_id' => $product_id,
            'body' => $request->input('comment'),
        ]);

        return redirect()->route('items.show', ['item_id' => $product_id]);
    }
}
