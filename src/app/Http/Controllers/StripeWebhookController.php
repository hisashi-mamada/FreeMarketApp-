<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\Webhook;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseDetail;
use App\Models\User;

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {

        $payload = $request->getContent();
        $event = json_decode($payload, true);

        if ($event['type'] === 'checkout.session.completed') {
            \Log::info('✅ Checkout Session Completed Webhook received');

            $session = $event['data']['object'];
            $metadata = $session['metadata'] ?? [];

            $productId = $metadata['product_id'] ?? null;
            $userId = $metadata['user_id'] ?? null;

            if ($productId && $userId) {
                $product = \App\Models\Product::find($productId);

                if ($product) {
                    $product->is_sold = true;
                    $product->save();

                    $purchase = \App\Models\Purchase::create([
                        'user_id' => $userId,
                        'product_id' => $product->id,
                        'address_id' => 1,
                        'payment_method' => 'card',
                    ]);

                    \App\Models\PurchaseDetail::create([
                        'purchase_id' => $purchase->id,
                        'product_id' => $product->id,
                        'quantity' => 1,
                        'subtotal' => $product->price,
                    ]);

                    \Log::info("✅ 購入処理完了：商品ID = {$productId}, ユーザーID = {$userId}");
                } else {
                    \Log::warning("⚠️ 商品IDが見つかりませんでした: {$productId}");
                }
            } else {
                \Log::warning("⚠️ metadataから情報が取得できませんでした");
            }
        }


        return response()->json(['status' => 'ok']);
    }
}
