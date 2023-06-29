<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Scopes\ReverseScope;
use Illuminate\Database\Eloquent\Relations\Relation;

class Post extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected static function booted(): void
    {
        static::addGlobalScope(new ReverseScope);
    }

    public function likes(): Relation
    {
        return $this->belongsToMany(User::class, 'likes', 'post_id', 'user_id');
    }

    function comments(): Relation 
    {
        return $this->hasMany(Comment::class);    
    }

    public function user(): Relation
    {
        return $this->belongsTo(User::class);
    }
}
