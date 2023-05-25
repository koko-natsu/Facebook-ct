<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Scopes\ReverseScope;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'body',
        'image',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new ReverseScope);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
