<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class ExploreController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('q', ''));

        $posts = Post::query()
            ->with(['user', 'comments.user', 'likes', 'favoritedBy'])
            ->withCount([
                'likes',
                'comments',
                'favoritedBy',
                'reports as active_reports_count' => function ($query) {
                    $query->whereIn('status', ['pending', 'accepted']);
                },
            ]);

        if (!auth()->check()) {
            $posts->visibleForVisitors();
        }

        if ($search !== '') {
            $posts->where(function ($query) use ($search) {
                $query->where('content', 'like', '%' . $search . '%')
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', '%' . $search . '%');
                    });
            });
        }

        $posts = $posts->latest()->get();

        return view('explore', compact('posts', 'search'));
    }
}
