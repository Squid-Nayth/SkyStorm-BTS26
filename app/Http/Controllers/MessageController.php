<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index()
    {
        $currentUser = auth()->user();

        $users = User::where('id', '!=', $currentUser->id)
            ->orderBy('name')
            ->get()
            ->map(function (User $user) use ($currentUser) {
                $lastMessage = Message::where(function ($query) use ($currentUser, $user) {
                    $query->where('sender_id', $currentUser->id)
                        ->where('recipient_id', $user->id);
                })->orWhere(function ($query) use ($currentUser, $user) {
                    $query->where('sender_id', $user->id)
                        ->where('recipient_id', $currentUser->id);
                })->latest()->first();

                $user->conversation_unread_count = Message::where('sender_id', $user->id)
                    ->where('recipient_id', $currentUser->id)
                    ->whereNull('read_at')
                    ->count();
                $user->last_conversation_message = $lastMessage;

                return $user;
            })
            ->sortByDesc(function (User $user) {
                return optional($user->last_conversation_message)->created_at;
            });

        return view('messages.index', compact('users'));
    }

    public function show(User $user)
    {
        abort_if($user->id === auth()->id(), 403);

        $currentUser = auth()->user();

        Message::where('sender_id', $user->id)
            ->where('recipient_id', $currentUser->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $messages = Message::where(function ($query) use ($currentUser, $user) {
            $query->where('sender_id', $currentUser->id)
                ->where('recipient_id', $user->id);
        })->orWhere(function ($query) use ($currentUser, $user) {
            $query->where('sender_id', $user->id)
                ->where('recipient_id', $currentUser->id);
        })->orderBy('created_at')->get();

        return view('messages.show', compact('user', 'messages'));
    }

    public function store(Request $request, User $user)
    {
        abort_if($user->id === auth()->id(), 403);

        $request->validate([
            'content' => 'required|string|max:500',
        ]);

        Message::create([
            'sender_id' => auth()->id(),
            'recipient_id' => $user->id,
            'content' => $request->input('content'),
        ]);

        return redirect()->route('messages.show', $user)->with('status', 'Message envoyé.');
    }
}
