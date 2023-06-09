<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserPostController;

Route::middleware('auth:api')->group(function () {

    Route::apiResources([
       'users' => UserController::class,
       'posts' => PostController::class,
       'users/{user}/posts' => UserPostController::class,
    ]);
});
