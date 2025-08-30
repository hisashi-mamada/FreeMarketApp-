<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use App\Models\Comment;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\ChatMessageRequest;
use Illuminate\Support\Facades\Gate;


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

    public function storeMessage(ChatMessageRequest $request, Product $product)
    {
        $validated = $request->validated();

        // 本文取得
        $body = $validated['message'];

        // 画像があれば保存
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('product_images', 'public');
        }

        // コメント保存
        Comment::create([
            'user_id' => auth()->id(),
            'product_id' => $product->id,
            'body' => $body,
            'image_path' => $imagePath,
        ]);

        return redirect()->back()->withInput(); // 入力保持のため
    }

    public function edit(Product $product, Comment $comment)
    {
        if (Auth::id() !== $comment->user_id) {
            abort(403, 'Unauthorized action.');
        }

        return redirect()->route('items.chat.show', ['product' => $product->id])
            ->with('editing_comment_id', $comment->id);
    }

    public function update(ChatMessageRequest $request, Product $product, Comment $comment)
    {
        if (Auth::id() !== $comment->user_id) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validated();

        $comment->body = $validated['message'];

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('chat_images', 'public');
            $comment->image_path = $path;
        }

        $comment->save();

        return redirect()->route('items.chat.show', ['product' => $product->id]);
    }

    public function destroy(Product $product, Comment $comment)
    {
        if (Auth::id() !== $comment->user_id) {
            abort(403, 'Unauthorized action.');
        }

        $comment->delete();

        return redirect()->route('items.chat.show', ['product' => $product->id]);
    }
}
