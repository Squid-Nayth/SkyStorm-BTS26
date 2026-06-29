@extends('layouts.app')

@section('content')
<div style="max-width: 1000px; margin: 0 auto;">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <h2 class="mb-1 ss-icon-label"><i class="bi bi-people"></i>Annuaire des membres</h2>
            <p class="text-muted mb-0">Recherche simple par nom, email ou localisation.</p>
        </div>
        <form action="{{ route('members.index') }}" method="GET" class="d-flex gap-2">
            <input type="text" name="q" value="{{ $search }}" class="form-control" placeholder="Rechercher un membre">
            <button type="submit" class="btn btn-primary ss-icon-label"><i class="bi bi-search"></i>Rechercher</button>
        </form>
    </div>

    <div class="row g-3">
        @forelse($users as $member)
            <div class="col-md-6">
                <div class="ss-card p-3 h-100">
                    <div class="d-flex gap-3 align-items-start">
                        @include('users._avatar', ['user' => $member, 'size' => 56])
                        <div class="flex-grow-1">
                            <div class="fw-semibold">{{ $member->name }}</div>
                            <div class="small text-muted">{{ $member->email }}</div>
                            @if($member->location)
                                <div class="small text-muted">{{ $member->location }}</div>
                            @endif
                            <div class="d-flex flex-wrap gap-2 mt-2">
                                <span class="badge text-bg-light ss-icon-label"><i class="bi bi-grid-3x3-gap"></i>{{ $member->posts_count }} posts</span>
                                <span class="badge text-bg-light ss-icon-label"><i class="bi bi-people"></i>{{ $member->followers_count }} abonnés</span>
                                <span class="badge text-bg-light ss-icon-label"><i class="bi bi-person-check"></i>{{ $member->following_count }} abonnements</span>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex gap-2 mt-3 flex-wrap">
                        <a href="{{ route('users.show', $member) }}" class="btn btn-sm btn-outline-primary ss-icon-label"><i class="bi bi-person-vcard"></i>Voir le profil</a>
                        @auth
                            @if(auth()->id() !== $member->id)
                                <a href="{{ route('messages.show', $member) }}" class="btn btn-sm btn-outline-success ss-icon-label"><i class="bi bi-chat-dots"></i>Message</a>
                            @endif
                        @endauth
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="ss-card p-4 text-center text-muted">Aucun membre trouvé.</div>
            </div>
        @endforelse
    </div>
</div>
@endsection
