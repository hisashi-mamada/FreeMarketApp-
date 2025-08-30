<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use App\Models\Comment;
use Illuminate\Support\Facades\Storage;


class ChatController extends Controller
{
    public function show(Product $product)
    {
        //dd($product); //
        //dd(Auth::id());

        $product->load(['user']);
        $user = Auth::user();
        $otherChatItems = $user->tradingProducts()->where('id', '!=', $product->id);

        // 仮で seller をログインユーザーに一致させる（確認目的）
        $product->user_id = $user->id;
        $product->save();

        $seller = $product->user;
        $isSeller = $user->id === $seller->id;
        $isBuyer = false;
        $partner = $seller;
        $isTradeComplete = false;
        $messages = Comment::with('user')
            ->where('product_id', $product->id)
            ->orderBy('created_at', 'asc')
            ->get();


        return view('items.chat', compact(
            'product',
            'partner',
            'messages',
            'isSeller',
            'isBuyer',
            'isTradeComplete',
            'otherChatItems'
        ));
    }

    public function store(Request $request, Product $product)
    {
        $request->validate([
            'message' => 'required_without:image|max:1000',
            'image' => 'nullable|image|max:2048',
        ]);

        $comment = new Comment();
        $comment->user_id = Auth::id();
        $comment->product_id = $product->id;
        $comment->body = $request->message;

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('chat_images', 'public');
            $comment->image_path = $path; // このカラムがマイグレーションに必要
        }

        $comment->save();

        return redirect()->route('items.chat.show', ['product' => $product->id]);

    }
}
