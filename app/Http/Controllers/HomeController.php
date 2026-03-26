<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = auth()->user();

        $followingIds = $user->following()->pluck('users.id');
        $feedIds = $followingIds->push($user->id);

        $posts = Post::whereIn('user_id', $feedIds)
            ->with('user')
            ->latest()
            ->get();

        $notes = $user->notes()->latest()->get();

        $suggestions = User::whereNotIn('id', $feedIds)->limit(5)->get();

        return view('home', compact('posts', 'notes', 'suggestions'));
    }
}
