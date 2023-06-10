<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use App\Models\Friend;
use App\Models\User;
use App\Http\Resources\Friend as FriendResource;
use App\Exceptions\UserNotFoundException;
use App\Exceptions\ValidationErrorException;
use Illuminate\Http\Resources\Json\JsonResource;

class FriendRequestController extends Controller
{
    public function store(): JsonResource
    {
        $data = request()->validate([
                'friend_id' => 'required',
        ]);

        try {
            User::findOrFail($data['friend_id'])
                ->friends()->syncWithoutDetaching(auth()->user());
        } 
        catch(ModelNotFoundException $e) {
            throw new UserNotFoundException();
        };

        return new FriendResource(
            Friend::where('user_id', auth()->id())
                ->where('friend_id', $data['friend_id'])
                ->first()
        );
    }

}
