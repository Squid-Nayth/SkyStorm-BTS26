@extends('layouts.app')

@section('content')
<div style="max-width: 900px; margin: 0 auto;">
    <div class="mb-4">
        <h2 class="mb-1 ss-icon-label"><i class="bi bi-people"></i>{{ $title }}</h2>
        <p class="text-muted mb-0">{{ $subtitle }}</p>
    </div>

    @forelse($users as $member)
        <div class="ss-card p-3 mb-3">
            <div class="d-flex justify-content-between align-items-center gap-3 flex-wrap">
                <div class="d-flex gap-3 align-items-center">
                    @include('users._avatar', ['user' => $member, 'size' => 52])
                    <div>
                        <div class="fw-semibold">{{ $member->name }}</div>
                        <div class="small text-muted">{{ $member->posts_count }} posts · {{ $member->followers_count }} abonnés</div>
                    </div>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{ route('users.show', $member) }}" class="btn btn-sm btn-outline-primary ss-icon-label"><i class="bi bi-person-vcard"></i>Profil</a>
                    @auth
                        @if(auth()->id() !== $member->id)
                            <a href="{{ route('messages.show', $member) }}" class="btn btn-sm btn-outline-success ss-icon-label"><i class="bi bi-chat-dots"></i>Message</a>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    @empty
        <div class="ss-card p-4 text-center text-muted">Aucun utilisateur dans cette liste.</div>
    @endforelse
</div>
@endsection
