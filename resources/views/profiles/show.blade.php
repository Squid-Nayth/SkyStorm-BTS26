@extends('layouts.app')

@section('content')
<div style="max-width: 950px; margin: 0 auto;">
    <div class="ss-card p-4 mb-4">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
            <div class="d-flex gap-3 align-items-start">
                @include('users._avatar', ['user' => $user, 'size' => 72])
                <div>
                    <h2 class="mb-1">{{ $user->name }}</h2>
                    <p class="text-muted mb-2">{{ $user->email }}</p>
                    @if($user->location || $user->website || $user->birthdate)
                        <div class="small text-muted mb-2">
                            @if($user->location)
                                <div>{{ $user->location }}</div>
                            @endif
                            @if($user->website)
                                <div><a href="{{ $user->website }}" target="_blank">{{ $user->website }}</a></div>
                            @endif
                            @if($user->birthdate)
                                <div>Né(e) le {{ $user->birthdate->format('d/m/Y') }}</div>
                            @endif
                        </div>
                    @endif
                    @if($user->bio)
                        <p class="mb-3" style="max-width: 620px;">{{ $user->bio }}</p>
                    @endif
                </div>
            </div>
            <div>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('users.followers', $user) }}" class="badge text-bg-light text-decoration-none ss-icon-label"><i class="bi bi-people"></i>{{ $stats['followers'] }} abonnés</a>
                    <a href="{{ route('users.following', $user) }}" class="badge text-bg-light text-decoration-none ss-icon-label"><i class="bi bi-person-check"></i>{{ $stats['following'] }} abonnements</a>
                    <span class="badge text-bg-light ss-icon-label"><i class="bi bi-grid-3x3-gap"></i>{{ $stats['posts'] }} posts</span>
                    <span class="badge text-bg-light ss-icon-label"><i class="bi bi-hand-thumbs-up"></i>{{ $stats['likes_received'] }} likes reçus</span>
                    <span class="badge text-bg-light ss-icon-label"><i class="bi bi-trophy"></i>Meilleur post : {{ $stats['best_post_likes'] }} likes</span>
                </div>
            </div>

            @auth
                @if(auth()->id() !== $user->id)
                    <a href="{{ route('messages.show', $user) }}" class="btn btn-outline-success ss-icon-label"><i class="bi bi-chat-dots"></i>Message</a>
                    @if(auth()->user()->isFollowing($user))
                        <form action="{{ route('users.unfollow', $user) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-secondary ss-icon-label"><i class="bi bi-person-dash"></i>Ne plus suivre</button>
                        </form>
                    @else
                        <form action="{{ route('users.follow', $user) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-primary ss-icon-label"><i class="bi bi-person-plus"></i>Suivre</button>
                        </form>
                    @endif
                @else
                    <a href="{{ route('profile.edit') }}" class="btn btn-outline-primary ss-icon-label"><i class="bi bi-pencil-square"></i>Modifier mon profil</a>
                @endif
            @endauth
        </div>

        @if($stats['favorites_public'])
            <div class="mt-3">
                <a href="{{ route('favorites.show', $user) }}" class="btn btn-outline-success btn-sm ss-icon-label"><i class="bi bi-bookmark-heart"></i>Voir ses favoris publics</a>
            </div>
        @endif
    </div>

    <h3 class="mb-3 ss-icon-label"><i class="bi bi-grid-3x3-gap"></i>Publications</h3>

    @forelse($posts as $post)
        @include('posts._card', ['post' => $post])
    @empty
        <div class="ss-card p-4 text-center text-muted">Aucune publication pour l'instant.</div>
    @endforelse
</div>
@endsection
