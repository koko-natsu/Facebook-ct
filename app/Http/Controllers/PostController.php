<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Post;
use App\Http\Resources\Post as PostResource;
use App\Http\Resources\PostCollection;


class PostController extends Controller
{

    public function index()
    {
        return new PostCollection(request()->user()->posts);
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
