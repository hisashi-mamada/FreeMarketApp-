<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Profile;

class MypageController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $profile = $user->profile;
        $tab = $request->query('tab');

        $products = [];
        $purchases = [];

        if ($tab === 'buy') {
            $purchases = $user->purchases()->with('purchaseDetails.product')->latest()->get();
        } elseif ($tab === 'sell') {
            $products = $user->products()->with('category')->latest()->get();
        }

        return view('items.mypage', compact('user', 'profile', 'tab', 'products', 'purchases'));
    }
}
