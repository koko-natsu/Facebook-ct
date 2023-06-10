<?php

namespace App\Http\Controllers;

use App\Http\Resources\CommentCollection;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostCommentController extends Controller
{
    public function store(Post $post): JsonResource
    {
        $data = request()->validate([
            'body' => 'required',
        ]);

        $post->comments()->create(array_merge($data, [
            'user_id' => auth()->id(),
        ]));

        return new CommentCollection($post->comments);
    }
}
