<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function store(Request $request, Post $post)
    {
        $request->validate([
            'reason' => 'required|string|max:255',
        ]);

        $alreadyReported = $post->reports()
            ->where('user_id', auth()->id())
            ->where('status', 'pending')
            ->exists();

        if ($alreadyReported) {
            return back()->with('status', 'Vous avez déjà signalé ce post.');
        }

        $post->reports()->create([
            'user_id' => auth()->id(),
            'reason' => $request->input('reason'),
            'status' => 'pending',
        ]);

        return back()->with('status', 'Signalement envoyé.');
    }
}
