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
        if ($user->id === auth()->id()) {
            return back()->with('status', 'Vous ne pouvez pas vous suivre vous-même.');
        }

        if (!auth()->user()->following()->where('following_id', $user->id)->exists()) {
            auth()->user()->following()->attach($user->id);
        }

        return back()->with('status', 'Utilisateur suivi.');
    }

    public function destroy(User $user)
    {
        auth()->user()->following()->detach($user->id);

        return back()->with('status', 'Utilisateur retiré de vos abonnements.');
    }
}
