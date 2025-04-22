<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Profile;

class MypageController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $profile = $user->profile;
        $tab = $request->query('tab');

        if ($tab === 'buy') {
            $purchases = $user->purchases()->with('purchaseDetails.product')->latest()->get();

            return view('mypage.buy', compact('purchases', 'profile', 'tab'));
        }

        if ($tab === 'sell') {
            $products = $user->products()->with('category')->latest()->get();
            return view('mypage.sell', compact('products', 'profile', 'tab'));
        }

        return view('mypage.index', compact('user', 'profile', 'tab'));
    }
}
