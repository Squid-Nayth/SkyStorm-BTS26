@extends('layouts.app')

@section('content')
<div class="row g-4" style="max-width: 1100px; margin: 0 auto;">

    {{-- ===== FEED PRINCIPAL ===== --}}
    <div class="col-lg-8">

        <div class="row g-3 mb-3">
            <div class="col-sm-6 col-xl-4">
                <div class="ss-card p-3">
                    <div class="small text-muted ss-icon-label"><i class="bi bi-people"></i>Abonnés</div>
                    <div class="fs-4 fw-bold">{{ $stats['followers'] }}</div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-4">
                <div class="ss-card p-3">
                    <div class="small text-muted ss-icon-label"><i class="bi bi-grid-3x3-gap"></i>Mes publications</div>
                    <div class="fs-4 fw-bold">{{ $stats['posts'] }}</div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-4">
                <div class="ss-card p-3">
                    <div class="small text-muted ss-icon-label"><i class="bi bi-hand-thumbs-up"></i>Likes reçus</div>
                    <div class="fs-4 fw-bold">{{ $stats['likes_received'] }}</div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-6">
                <div class="ss-card p-3">
                    <div class="small text-muted ss-icon-label"><i class="bi bi-trophy"></i>Record sur un post</div>
                    <div class="fs-4 fw-bold">{{ $stats['best_post_likes'] }}</div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-6">
                <div class="ss-card p-3">
                    <div class="small text-muted ss-icon-label"><i class="bi bi-bookmark-heart"></i>Favoris enregistrés</div>
                    <div class="fs-4 fw-bold">{{ $stats['favorites'] }}</div>
                </div>
            </div>
        </div>

        {{-- Compositeur de post --}}
        <div class="ss-card p-3 mb-3">
            <form action="{{ route('posts.store') }}" method="POST">
                @csrf
                <div class="d-flex align-items-start gap-3">
                    <div style="margin-top: 2px;">
                        @include('users._avatar', ['user' => auth()->user(), 'size' => 38])
                    </div>
                    <textarea name="content" rows="2" placeholder="Quoi de neuf ?" maxlength="255"
                              class="form-control border-0 @error('content') is-invalid @enderror"
                              style="resize: none; background: #f9fafb; border-radius: 0.5rem; font-family: inherit;">{{ old('content') }}</textarea>
                </div>
                @error('content')
                    <div class="text-danger small mt-1" style="padding-left: 50px;">{{ $message }}</div>
                @enderror
                <div class="d-flex justify-content-end mt-2">
                    <button type="submit" class="btn btn-sm text-white"
                            style="background: #3b6fd4; border-radius: 9999px; padding: 0.3rem 1.4rem; border: none; font-family: inherit;">
                        <span class="ss-icon-label"><i class="bi bi-send"></i>Publier</span>
                    </button>
                </div>
            </form>
        </div>

        {{-- Fil des posts --}}
        @forelse($posts as $post)
            @include('posts._card', ['post' => $post])
        @empty
            <div class="ss-card p-4 text-center" style="color: #9ca3af;">
                Aucun post pour l'instant. Suivez des utilisateurs ou publiez votre premier post !
            </div>
        @endforelse

    </div>

    {{-- ===== PANNEAU DROIT ===== --}}
    <div class="col-lg-4">

        {{-- Mes notes privées --}}
        <div class="ss-card p-3 mb-3">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="mb-0 fw-bold" style="color: #1f2937;">Mes notes</h6>
                <a href="{{ route('notes.create') }}"
                   class="btn btn-sm text-white"
                   style="background: #3b6fd4; border-radius: 9999px; font-size: 0.75rem; padding: 0.2rem 0.8rem; border: none;">
                    <span class="ss-icon-label"><i class="bi bi-plus-lg"></i>Nouvelle</span>
                </a>
            </div>

            @forelse($notes->take(4) as $note)
                <div class="d-flex justify-content-between align-items-start py-2 {{ !$loop->last ? 'border-bottom' : '' }}"
                     style="border-color: #f3f4f6 !important;">
                    <div style="min-width: 0; margin-right: 0.5rem;">
                        <div class="small fw-semibold text-truncate" style="color: #1f2937;">
                            {{ \Illuminate\Support\Str::limit($note->titre, 28) }}
                        </div>
                        <div class="small" style="color: #9ca3af;">{{ $note->created_at->format('d/m/Y') }}</div>
                    </div>
                    <a href="{{ route('notes.edit', $note) }}"
                       class="small text-decoration-none flex-shrink-0"
                       style="color: #3b6fd4;">Éditer</a>
                </div>
            @empty
                <p class="small mb-0" style="color: #9ca3af;">Aucune note pour l'instant.</p>
            @endforelse

            @if($notes->count() > 4)
                <a href="{{ route('notes.index') }}"
                   class="d-block text-center small mt-2 text-decoration-none"
                   style="color: #3b6fd4;">
                    Voir toutes les notes ({{ $notes->count() }})
                </a>
            @endif
        </div>

        <div class="ss-card p-3 mb-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="fw-bold mb-1" style="color: #1f2937;">Mes favoris</h6>
                    <div class="small text-muted">
                        Liste {{ auth()->user()->favorite_posts_public ? 'publique' : 'privée' }}
                    </div>
                </div>
                <a href="{{ route('favorites.index') }}" class="btn btn-sm btn-outline-success">Ouvrir</a>
            </div>
        </div>

        {{-- Suggestions d'utilisateurs à suivre --}}
        @if($suggestions->count() > 0)
        <div class="ss-card p-3">
            <h6 class="fw-bold mb-3" style="color: #1f2937;">Suggestions</h6>

            @foreach($suggestions as $suggestion)
                <div class="d-flex align-items-center gap-2 {{ !$loop->last ? 'mb-3' : '' }}">
                    @include('users._avatar', ['user' => $suggestion, 'size' => 34])
                    <div class="flex-grow-1" style="min-width: 0;">
                        <div class="small fw-semibold text-truncate" style="color: #1f2937;">
                            <a href="{{ route('users.show', $suggestion) }}" class="text-decoration-none" style="color: inherit;">
                                {{ $suggestion->name }}
                            </a>
                        </div>
                    </div>
                    <form action="{{ route('users.follow', $suggestion) }}" method="POST" class="flex-shrink-0">
                        @csrf
                        <button type="submit"
                                class="btn btn-sm"
                                style="background: #fff; border: 1.5px solid #3b6fd4; color: #3b6fd4; border-radius: 0.5rem; font-size: 0.8rem; padding: 0.2rem 0.7rem; white-space: nowrap; font-family: inherit;">
                            <span class="ss-icon-label"><i class="bi bi-person-plus"></i>Suivre</span>
                        </button>
                    </form>
                </div>
            @endforeach
        </div>
        @endif

    </div>

</div>
@endsection
