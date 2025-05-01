<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'name',
        'description',
        'price',
        'image_url',
        'condition',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function purchaseDetail()
    {
        return $this->hasOne(\App\Models\PurchaseDetail::class);
    }

    public function isSold()
    {
        return $this->purchaseDetail()->exists();
    }

    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'favorites')->withTimestamps();
    }

    public function isFavoritedBy($user)
    {
        return $this->favoritedUsers()->where('user_id', $user->id)->exists();
    }

    public function favoritedUsers()
    {
        return $this->belongsToMany(User::class, 'favorites');
    }
}
