<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserPostController;
use App\Http\Controllers\AuthUserController;
use App\Http\Controllers\FriendRequestController;

Route::middleware('auth:api')->group(function () {

    Route::get('auth-user', [AuthUserController::class, 'show']);

    Route::apiResources([
       'users' => UserController::class,
       'posts' => PostController::class,
       'users/{user}/posts' => UserPostController::class,
       '/friend-request' => FriendRequestController::class,
    ]);
});
