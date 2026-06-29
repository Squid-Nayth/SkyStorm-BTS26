<?php

namespace App\Http\Controllers;

use App\Models\Post;

class LikeController extends Controller
{
    public function store(Post $post)
    {
        auth()->user()->likedPosts()->syncWithoutDetaching([$post->id]);

        return back()->with('status', 'Like ajouté.');
    }

    public function destroy(Post $post)
    {
        auth()->user()->likedPosts()->detach($post->id);

        return back()->with('status', 'Like retiré.');
    }
}
