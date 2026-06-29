@extends('layouts.app')

@section('content')
<div style="max-width: 900px; margin: 0 auto;">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div class="d-flex align-items-center gap-3">
            @include('users._avatar', ['user' => $user, 'size' => 56])
            <div>
                <h2 class="mb-1">Discussion avec {{ $user->name }}</h2>
                <div class="small text-muted">{{ $user->email }}</div>
            </div>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('users.show', $user) }}" class="btn btn-outline-primary btn-sm ss-icon-label"><i class="bi bi-person-vcard"></i>Voir le profil</a>
            <a href="{{ route('messages.index') }}" class="btn btn-outline-secondary btn-sm ss-icon-label"><i class="bi bi-list-ul"></i>Toutes les discussions</a>
        </div>
    </div>

    <div class="ss-card p-3 mb-3" style="min-height: 320px;">
        @forelse($messages as $message)
            <div class="d-flex mb-3 {{ $message->sender_id === auth()->id() ? 'justify-content-end' : 'justify-content-start' }}">
                <div class="px-3 py-2 rounded" style="max-width: 75%; background: {{ $message->sender_id === auth()->id() ? '#dbeafe' : '#f3f4f6' }};">
                    <div class="small mb-1 fw-semibold">
                        {{ $message->sender_id === auth()->id() ? 'Moi' : $user->name }}
                    </div>
                    <div>{{ $message->content }}</div>
                    <div class="small text-muted mt-1">
                        {{ $message->created_at->format('d/m/Y H:i') }}
                        @if($message->sender_id === auth()->id())
                            · {{ $message->read_at ? 'Lu' : 'Envoyé' }}
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center text-muted py-5">Aucun message pour le moment. Envoyez le premier.</div>
        @endforelse
    </div>

    <div class="ss-card p-3">
        <form action="{{ route('messages.store', $user) }}" method="POST">
            @csrf
            <div class="d-flex gap-2">
                <textarea name="content" rows="3" maxlength="500" class="form-control" placeholder="Écrire un message..." required>{{ old('content') }}</textarea>
                <button type="submit" class="btn btn-primary ss-icon-label"><i class="bi bi-send"></i>Envoyer</button>
            </div>
        </form>
    </div>
</div>
@endsection
