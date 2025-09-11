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

        // 受け取った評価（出品者として：購入者→自分）
        $asSellerQ = \App\Models\PurchaseDetail::whereHas('product', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })->whereNotNull('buyer_rating');

        // 受け取った評価（購入者として：出品者→自分）
        $asBuyerQ = \App\Models\PurchaseDetail::whereHas('purchase', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })->whereNotNull('seller_rating');

        $asSellerAvg   = (float) $asSellerQ->avg('buyer_rating');
        $asSellerCount = (int)   $asSellerQ->count();
        $asBuyerAvg    = (float) $asBuyerQ->avg('seller_rating');
        $asBuyerCount  = (int)   $asBuyerQ->count();

        $totalCount = $asSellerCount + $asBuyerCount;
        $averageRating = null;
        if ($totalCount > 0) {
            // 件数で重み付けした平均（小数1位）
            $sum = $asSellerAvg * $asSellerCount + $asBuyerAvg * $asBuyerCount;
            $averageRating = round($sum / $totalCount, 1);
        }

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

        return view('items.mypage', compact('user', 'profile', 'tab', 'products', 'purchases', 'allCategories', 'chatItems', 'averageRating', 'unreadTotal', 'totalCount'));
    }
}
