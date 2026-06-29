<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $posts = $user->favoritePosts()
            ->with(['user', 'comments.user', 'likes', 'favoritedBy'])
            ->withCount([
                'likes',
                'comments',
                'favoritedBy',
                'reports as active_reports_count' => function ($query) {
                    $query->whereIn('status', ['pending', 'accepted']);
                },
            ])
            ->latest('favorite_posts.created_at')
            ->get();

        return view('favorites.index', compact('user', 'posts'));
    }

    public function show(User $user)
    {
        if (!$user->favorite_posts_public && (!auth()->check() || auth()->id() !== $user->id)) {
            abort(403);
        }

        $posts = $user->favoritePosts()
            ->with(['user', 'comments.user', 'likes', 'favoritedBy'])
            ->withCount([
                'likes',
                'comments',
                'favoritedBy',
                'reports as active_reports_count' => function ($query) {
                    $query->whereIn('status', ['pending', 'accepted']);
                },
            ])
            ->latest('favorite_posts.created_at');

        if (!auth()->check()) {
            $posts->visibleForVisitors();
        }

        $posts = $posts->get();

        return view('favorites.show', compact('user', 'posts'));
    }

    public function store(Post $post)
    {
        $user = auth()->user();

        if (!$user->favoritePosts()->where('favorite_posts.post_id', $post->id)->exists() && $user->favoritePosts()->count() >= 50) {
            return back()->with('status', 'Vous avez déjà 50 favoris.');
        }

        $user->favoritePosts()->syncWithoutDetaching([$post->id]);

        return back()->with('status', 'Post ajouté aux favoris.');
    }

    public function destroy(Post $post)
    {
        auth()->user()->favoritePosts()->detach($post->id);

        return back()->with('status', 'Post retiré des favoris.');
    }

    public function updateVisibility(Request $request)
    {
        $user = auth()->user();
        $user->update([
            'favorite_posts_public' => $request->boolean('favorite_posts_public'),
        ]);

        return back()->with('status', 'Visibilité des favoris mise à jour.');
    }
}
