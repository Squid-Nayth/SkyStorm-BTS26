<?php

namespace App\Http\Controllers;

use App\Models\User;

class FollowController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(User $user)
    {
        if (!auth()->user()->following()->where('following_id', $user->id)->exists()) {
            auth()->user()->following()->attach($user->id);
        }
        return back();
    }

    public function destroy(User $user)
    {
        auth()->user()->following()->detach($user->id);
        return back();
    }
}
