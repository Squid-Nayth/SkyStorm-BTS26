@extends('layouts.app')

@section('content')
<div style="max-width: 900px; margin: 0 auto;">
    <div class="mb-4">
        <h2 class="mb-1 ss-icon-label"><i class="bi bi-chat-dots"></i>Messagerie</h2>
        <p class="text-muted mb-0">Conversations privées simples entre utilisateurs.</p>
    </div>

    @forelse($users as $user)
        <div class="ss-card p-3 mb-3">
            <div class="d-flex justify-content-between align-items-center gap-3 flex-wrap">
                <div class="d-flex align-items-center gap-3">
                    @include('users._avatar', ['user' => $user, 'size' => 50])
                    <div>
                        <div class="fw-semibold">{{ $user->name }}</div>
                        @if($user->last_conversation_message)
                            <div class="small text-muted">
                                Dernier message : {{ \Illuminate\Support\Str::limit($user->last_conversation_message->content, 60) }}
                            </div>
                        @else
                            <div class="small text-muted">Aucune conversation pour le moment.</div>
                        @endif
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    @if($user->conversation_unread_count > 0)
                        <span class="badge text-bg-danger">{{ $user->conversation_unread_count }} non lus</span>
                    @endif
                    <a href="{{ route('messages.show', $user) }}" class="btn btn-sm btn-primary ss-icon-label"><i class="bi bi-arrow-right-circle"></i>Ouvrir</a>
                </div>
            </div>
        </div>
    @empty
        <div class="ss-card p-4 text-center text-muted">Aucun autre utilisateur disponible pour discuter.</div>
    @endforelse
</div>
@endsection
