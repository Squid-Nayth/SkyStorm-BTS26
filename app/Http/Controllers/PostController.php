<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Request $request)
    {
        $request->validate([
            'content' => 'required|max:500',
        ]);

        auth()->user()->posts()->create($request->only('content'));

        return redirect()->route('home')->with('status', 'Post publié !');
    }

    public function destroy(Post $post)
    {
        if ($post->user_id !== auth()->id()) {
            abort(403);
        }
        $post->delete();

        return redirect()->route('home')->with('status', 'Post supprimé.');
    }
}
