<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\PurchaseDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class RatingController extends Controller
{
    public function store(Request $request, Product $product)
    {
        $data = $request->validate([
            'score'   => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        // 対象の取引（この商品に紐づく purchase_details の最新1件を取得）
        $detail = PurchaseDetail::with('purchase')
            ->where('product_id', $product->id)
            ->latest('id')
            ->firstOrFail();

        $buyerId  = optional($detail->purchase)->user_id; // 購入者
        $sellerId = $product->user_id;                     // 出品者
        $uid      = Auth::id();

        $wasBuyerRatedBefore = !is_null($detail->buyer_rating);

        // ロールに応じて保存先のカラムを分岐＆権限チェック
        if ($uid === $buyerId) {
            // 購入者：自分が未評価のときのみ
            if (!is_null($detail->buyer_rating)) {
                abort(403, 'すでに評価済みです。');
            }
            $detail->buyer_rating         = $data['score'];
            $detail->buyer_rating_comment = $data['comment'] ?? null;
            $detail->buyer_rated_at       = now();
        } elseif ($uid === $sellerId) {
            // 出品者：購入者が先に評価済み、かつ自分は未評価のときのみ
            if (is_null($detail->buyer_rating) || !is_null($detail->seller_rating)) {
                abort(403, '評価できる状態ではありません。');
            }
            $detail->seller_rating         = $data['score'];
            $detail->seller_rating_comment = $data['comment'] ?? null;
            $detail->seller_rated_at       = now();
        } else {
            // 取引当事者以外は評価不可
            abort(403, 'この取引の評価権限がありません。');
        }

        $detail->save();

        if ($uid === $buyerId) {
            $seller = $product->user ?? \App\Models\User::find($product->user_id);
            if ($seller && $seller->email) {
                $subject = '【取引完了のお知らせ】' . $product->name;
                $buyer   = Auth::user();
                $homeUrl = url('/'); // 遷移先はホーム

                Mail::raw(
                    "出品された商品「{$product->name}」について、購入者 {$buyer->name} さんが取引を完了しました。\n\n"
                        . "出品者としての評価がまだの場合は、アプリから評価をお願いします。\n\n"
                        . "ホーム画面へ：{$homeUrl}\n",
                    function ($message) use ($seller, $subject) {
                        $message->to($seller->email)->subject($subject);
                    }
                );
            }
        }

        return redirect()->route('items.index')->with('status', '評価を送信しました。');
    }
}
