<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Post;
use App\Models\Friend;
use App\Http\Resources\Post as PostResource;
use App\Http\Resources\PostCollection;


class PostController extends Controller
{

    public function index()
    {
        $friends = Friend::friendships();

        if ($friends->isEmpty()) {
            return new PostCollection(request()->user()->posts);
        }

        
        return new PostCollection(
            Post::whereIn('user_id', [$friends->pluck('user_id'), $friends->pluck('friend_id')])
            ->get()
        );

    }


    public function store(Request $request): PostResource
    {
        $data = $request->validate([
            'data.attributes.body' => '',
            'data.attributes.image' => '',
        ]);

        $post = $request->user()->posts()->create($data['data']['attributes']);

        return new PostResource($post);
    }
}
