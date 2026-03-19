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

    public function index()
    {
        $posts = auth()->user()->posts()->latest()->get();
        return view('posts.index', compact('posts'));
    }

    public function create()
    {
        return view('posts.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'content' => 'required|max:500',
        ]);

        auth()->user()->posts()->create($request->only('content'));

        return redirect()->route('posts.index')->with('status', 'Post publié !');
    }

    public function edit(Post $post)
    {
        if ($post->user_id !== auth()->id()) {
            abort(403);
        }
        return view('posts.edit', compact('post'));
    }

    public function update(Request $request, Post $post)
    {
        if ($post->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'content' => 'required|max:500',
        ]);

        $post->update($request->only('content'));

        return redirect()->route('posts.index')->with('status', 'Post modifié !');
    }

    public function destroy(Post $post)
    {
        if ($post->user_id !== auth()->id()) {
            abort(403);
        }
        $post->delete();

        return redirect()->route('posts.index')->with('status', 'Post supprimé.');
    }
}
