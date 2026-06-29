<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request, Post $post)
    {
        $request->validate([
            'content' => 'required|string|max:255',
        ]);

        $post->comments()->create([
            'content' => $request->input('content'),
            'user_id' => auth()->id(),
        ]);

        return back()->with('status', 'Commentaire ajouté.');
    }

    public function destroy(Comment $comment)
    {
        $user = auth()->user();

        if ($comment->user_id !== $user->id && $comment->post->user_id !== $user->id) {
            abort(403);
        }

        $comment->delete();

        return back()->with('status', 'Commentaire supprimé.');
    }
}
