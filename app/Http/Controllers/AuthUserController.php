<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\User as UserResource;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthUserController extends Controller
{
    
    public function show(): JsonResource
    {
        return new UserResource(auth()->user());
    }
}
