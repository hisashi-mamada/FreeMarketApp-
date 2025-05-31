<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseDetail;
use App\Models\Address;
use Illuminate\Support\Facades\Log;

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        Log::info('🚀 Webhook handle() START');

        $payload = $request->getContent();
        $event = json_decode($payload, true);

        if ($event['type'] === 'checkout.session.completed') {
            Log::info('✅ Checkout Session Completed Webhook received');

            $session = $event['data']['object'];
            $metadata = $session['metadata'] ?? [];

            $productId = $metadata['product_id'] ?? null;
            $userId = $metadata['user_id'] ?? null;

            if ($productId && $userId) {
                $product = Product::find($productId);

                if ($product) {

                    $product->is_sold = true;
                    $product->save();

                    $address = Address::where('user_id', $userId)->latest()->first();

                    if ($address) {
                        $purchase = Purchase::create([
                            'user_id' => $userId,
                            'address_id' => $address->id,
                            'payment_method' => 'card',
                        ]);


                        PurchaseDetail::create([
                            'purchase_id' => $purchase->id,
                            'product_id' => $product->id,
                            'quantity' => 1,
                            'subtotal' => (float) $product->price,
                        ]);

                        Log::info("✅ 購入処理完了：商品ID = {$productId}, ユーザーID = {$userId}");
                    } else {
                        Log::warning("⚠️ ユーザー {$userId} にアドレスが見つかりませんでした");
                    }
                } else {
                    Log::warning("⚠️ 商品IDが見つかりませんでした: {$productId}");
                }
            } else {
                Log::warning("⚠️ metadataから情報が取得できませんでした");
            }
        }

        return response()->json(['status' => 'ok']);
    }
}
