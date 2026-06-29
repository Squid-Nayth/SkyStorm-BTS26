<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;

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
            ->with(['user', 'comments.user', 'likes', 'favoritedBy'])
            ->withCount([
                'likes',
                'comments',
                'favoritedBy',
                'reports as active_reports_count' => function ($query) {
                    $query->whereIn('status', ['pending', 'accepted']);
                },
            ])
            ->latest()
            ->get();

        $notes = $user->notes()->latest()->get();

        $suggestions = User::whereNotIn('id', $feedIds)->limit(5)->get();

        $postsStats = $user->posts()->withCount('likes')->get();

        $stats = [
            'followers' => $user->followers()->count(),
            'posts' => $user->posts()->count(),
            'likes_received' => $postsStats->sum('likes_count'),
            'best_post_likes' => $postsStats->max('likes_count') ?? 0,
            'favorites' => $user->favoritePosts()->count(),
        ];

        return view('home', compact('posts', 'notes', 'suggestions', 'stats'));
    }
}
