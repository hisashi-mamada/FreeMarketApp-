<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use App\Models\Comment;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\ChatMessageRequest;
use Illuminate\Support\Facades\Gate;
use App\Models\PurchaseDetail;
use App\Models\Purchase;
use App\Models\User;

class ChatController extends Controller
{
    public function show(Product $product)
    {
        $product->load(['user']);
        $user = Auth::user();

        $otherChatItems = $user->tradingProducts()->where('id', '!=', $product->id);

        // 出品者
        $sellerId = $product->user_id;
        $seller   = $sellerId ? User::find($sellerId) : null;

        // この商品の purchase_details（最新1件想定）
        $detail = \App\Models\PurchaseDetail::with('purchase')
            ->where('product_id', $product->id)
            ->latest('id')
            ->first();

        // 購入者ID（$detailやpurchaseがnullでも安全）
        $buyerId = data_get($detail, 'purchase.user_id');

        // 役割判定
        $isSeller = $user->id === $sellerId;
        $isBuyer  = $buyerId !== null && $user->id === (int)$buyerId;

        $force = session('force_rating_modal', false);

        // モーダル表示条件（PHP7でもOKな比較に）
        $showBuyerModal  = $isBuyer
            && $detail
            && $detail->buyer_rating === null
            && $force;
        $showSellerModal = $isSeller && $detail && $detail->buyer_rating !== null && $detail->seller_rating === null;

        // 取引完了（両者評価済み）
        $isTradeComplete = (bool) ($detail && $detail->buyer_rating !== null && $detail->seller_rating !== null);

        // 相手ユーザー
        $partner = $isSeller
            ? ($buyerId ? \App\Models\User::find($buyerId) : $seller)
            : $seller;

        // メッセージ
        $messages = \App\Models\Comment::with('user')
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
            'otherChatItems',
            'detail',
            'showBuyerModal',
            'showSellerModal',
            'buyerId'
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

    public function complete(Product $product)
    {
        // この商品の取引詳細を取得
        $detail = PurchaseDetail::with('purchase')
            ->where('product_id', $product->id)
            ->latest('id')
            ->firstOrFail();

        $buyerId = data_get($detail, 'purchase.user_id');

        // 購入者だけが「取引完了」を押せる
        if (Auth::id() !== (int)$buyerId) {
            abort(403, '購入者のみが取引完了できます。');
        }

        // モーダルを出すためのフラグをセッションにセットしてチャットへ戻る
        return redirect()
            ->route('items.chat.show', ['product' => $product->id])
            ->with('force_rating_modal', true);
    }
}
