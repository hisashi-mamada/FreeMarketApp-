<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    public function favorites()
    {
        return $this->belongsToMany(Product::class, 'favorites')->withTimestamps();
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function tradingProductsQuery()
    {
        return \App\Models\Product::where(function ($q) {
            // 自分が出品者の取引（購入詳細がある＝取引が始まっている）
            $q->where('user_id', $this->id)
                ->whereHas('purchaseDetails');
        })
            ->orWhere(function ($q) {
                // 自分が購入者の取引
                $q->whereHas('purchaseDetails.purchase', function ($qq) {
                    $qq->where('user_id', $this->id);
                });
            })
            // 取引完了も含めたいならこのまま。未完了だけにしたいなら下の“未完了だけ”を採用
            ->orderByDesc('id');
    }


    public function tradingProducts()
    {
        return Product::whereHas('comments', function ($query) {
            $query->where('user_id', $this->id);
        })->get();
    }


    public function tradingProductsSorted()
    {
        return Product::whereHas('comments', function ($query) {
            $query->where('user_id', $this->id);
        })
            ->withCount(['comments as latest_comment_at' => function ($query) {
                $query->select(\DB::raw('MAX(created_at)'));
            }])
            ->orderByDesc('latest_comment_at')
            ->get();
    }

    public function averageRating()
    {
        return $this->products()
            ->whereHas('comments', function ($query) {
                $query->whereNotNull('rating');
            })
            ->with('comments')
            ->get()
            ->flatMap(function ($product) {
                return $product->comments->pluck('rating');
            })
            ->avg();
    }

    public function roundedAverageRating()
    {
        $ratings = $this->products()
            ->with('comments')
            ->get()
            ->flatMap(function ($product) {
                return $product->comments->pluck('rating');
            })
            ->filter();

        if ($ratings->isEmpty()) {
            return null;
        }

        return round($ratings->average());
    }
}
