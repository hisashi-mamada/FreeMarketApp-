<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRatingsToPurchaseDetailsTable extends Migration
{
    public function up(): void
    {
        Schema::table('purchase_details', function (Blueprint $table) {
            // 購入者 → 出品者への評価
            $table->unsignedTinyInteger('buyer_rating')->nullable();
            $table->text('buyer_rating_comment')->nullable();
            $table->timestamp('buyer_rated_at')->nullable();

            // 出品者 → 購入者への評価
            $table->unsignedTinyInteger('seller_rating')->nullable();
            $table->text('seller_rating_comment')->nullable();
            $table->timestamp('seller_rated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('purchase_details', function (Blueprint $table) {
            $table->dropColumn([
                'buyer_rating',
                'buyer_rating_comment',
                'buyer_rated_at',
                'seller_rating',
                'seller_rating_comment',
                'seller_rated_at',
            ]);
        });
    }
}
