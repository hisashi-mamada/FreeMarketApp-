<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Profile;
use App\Models\User;
use App\Models\Product;
use App\Models\Comment;

class MypageController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $profile = $user->profile;
        $tab = $request->query('tab', 'sell');

        $unreadTotal = Comment::where('user_id', '!=', $user->id)
            ->whereIn('product_id', $user->tradingProductsQuery()->select('products.id'))
            ->count();

        $products = [];
        $purchases = [];
        $chatItems = [];

        if ($tab === 'buy') {
            $purchases = $user->purchases()->with('purchaseDetails.product')->latest()->get();
        } elseif ($tab === 'sell') {
            $products = $user->products()->latest()->get();
        } elseif ($tab === 'chat') {
            $chatItems = $user->tradingProductsQuery()
                ->with(['comments' => function ($query) {
                    $query->latest(); // コメントを新しい順で取得
                }])
                ->get()
                ->sortByDesc(function ($product) {
                    return optional($product->comments->first())->created_at;
                })
                ->map(function ($product) use ($user) {
                    $unreadCount = $product->comments
                        ->where('user_id', '!=', $user->id)
                        ->count();
                    $product->unread_count = $unreadCount;
                    return $product;
                });
        }


        $allCategories = Category::pluck('name', 'id')->toArray();
        $averageRating = $user->roundedAverageRating();

        return view('items.mypage', compact('user', 'profile', 'tab', 'products', 'purchases', 'allCategories', 'chatItems', 'averageRating', 'unreadTotal'));
    }
}
