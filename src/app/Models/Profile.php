<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nickname',
        'phone',
        'postal_code',
        'address',
        'building',
        'image_path',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
