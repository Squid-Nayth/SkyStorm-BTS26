<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('q', ''));

        $users = User::query()
            ->withCount(['posts', 'followers', 'following']);

        if ($search !== '') {
            $users->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('location', 'like', '%' . $search . '%');
            });
        }

        $users = $users->orderBy('name')->get();

        return view('profiles.index', compact('users', 'search'));
    }

    public function show(User $user)
    {
        $posts = $user->posts()
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

        $postsStats = $user->posts()->withCount('likes')->get();

        $stats = [
            'followers' => $user->followers()->count(),
            'following' => $user->following()->count(),
            'posts' => $user->posts()->count(),
            'likes_received' => $postsStats->sum('likes_count'),
            'best_post_likes' => $postsStats->max('likes_count') ?? 0,
            'favorites_public' => $user->favorite_posts_public,
        ];

        return view('profiles.show', compact('user', 'posts', 'stats'));
    }

    public function followers(User $user)
    {
        $users = $user->followers()->withCount(['posts', 'followers'])->orderBy('name')->get();

        return view('profiles.relations', [
            'title' => 'Abonnés de ' . $user->name,
            'subtitle' => 'Utilisateurs qui suivent ce profil',
            'owner' => $user,
            'users' => $users,
        ]);
    }

    public function following(User $user)
    {
        $users = $user->following()->withCount(['posts', 'followers'])->orderBy('name')->get();

        return view('profiles.relations', [
            'title' => 'Abonnements de ' . $user->name,
            'subtitle' => 'Utilisateurs suivis par ce profil',
            'owner' => $user,
            'users' => $users,
        ]);
    }
}
