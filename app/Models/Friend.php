<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Friend extends Model
{
    use HasFactory;

    protected $fillable = [
        'friend_id',
        'user_id',
        'confirmed_at',
        'status',
    ];

    protected $dates = ['confirmed_at'];

    public static function friendship(string $userId)
    {
        return (new static)
            ->where(function ($query) use($userId) {
                return $query->where('user_id', auth()->id())
                    ->where('friend_id', $userId);
            })
            ->orWhere(function ($query) use($userId) {
                return $query->where('friend_id', auth()->id())
                    ->where('user_id', $userId);
            })
            ->first();
    }


    public static function friendships()
    {
        return (new static())
            ->whereNotNull('confirmed_at')
            ->where(function ($query) {
                return $query->where('user_id', auth()->id())
                    ->orWhere('friend_id', auth()->id());             
            })
            ->get();
    }
}
