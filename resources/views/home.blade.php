@extends('layouts.app')

@section('content')
<div class="row g-4" style="max-width: 1100px; margin: 0 auto;">

    {{-- ===== FEED PRINCIPAL ===== --}}
    <div class="col-lg-8">

        @if (session('status'))
            <div class="alert alert-success alert-dismissible fade show mb-3" role="alert" style="border-radius: 0.75rem;">
                {{ session('status') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Compositeur de post --}}
        <div class="ss-card p-3 mb-3">
            @php $initials = strtoupper(substr(auth()->user()->name, 0, 2)); @endphp
            <form action="{{ route('posts.store') }}" method="POST">
                @csrf
                <div class="d-flex align-items-start gap-3">
                    <div class="ss-avatar text-white flex-shrink-0" style="background: #3b6fd4; margin-top: 2px;">
                        {{ $initials }}
                    </div>
                    <textarea name="content" rows="2" placeholder="Quoi de neuf ?"
                              class="form-control border-0 @error('content') is-invalid @enderror"
                              style="resize: none; background: #f9fafb; border-radius: 0.5rem; font-family: inherit;">{{ old('content') }}</textarea>
                </div>
                @error('content')
                    <div class="text-danger small mt-1" style="padding-left: 50px;">{{ $message }}</div>
                @enderror
                <div class="d-flex justify-content-end mt-2">
                    <button type="submit" class="btn btn-sm text-white"
                            style="background: #3b6fd4; border-radius: 9999px; padding: 0.3rem 1.4rem; border: none; font-family: inherit;">
                        Publier
                    </button>
                </div>
            </form>
        </div>

        {{-- Fil des posts --}}
        @forelse($posts as $post)
            @php
                $palette = ['#3b6fd4','#10b981','#f59e0b','#ef4444','#8b5cf6','#ec4899'];
                $avatarColor = $palette[$post->user->id % count($palette)];
                $postInitials = strtoupper(substr($post->user->name, 0, 2));
                $isOwn = $post->user_id === auth()->id();
            @endphp
            <div class="ss-card p-3 mb-3">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="d-flex gap-3">
                        <div class="ss-avatar text-white flex-shrink-0" style="background: {{ $avatarColor }};">
                            {{ $postInitials }}
                        </div>
                        <div>
                            <div class="fw-semibold" style="color: #1f2937; line-height: 1.3;">{{ $post->user->name }}</div>
                            <div class="small" style="color: #9ca3af;">{{ $post->created_at->format('d/m/Y · H:i') }}</div>
                        </div>
                    </div>
                    @if($isOwn)
                        <div class="d-flex gap-2 flex-shrink-0">
                            <a href="{{ route('posts.edit', $post) }}"
                               class="btn btn-sm btn-outline-secondary py-0 px-2"
                               style="font-size: 0.75rem; border-radius: 0.4rem;">Modifier</a>
                            <form action="{{ route('posts.destroy', $post) }}" method="POST"
                                  onsubmit="return confirm('Supprimer ce post ?')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="btn btn-sm btn-outline-danger py-0 px-2"
                                        style="font-size: 0.75rem; border-radius: 0.4rem;">Supprimer</button>
                            </form>
                        </div>
                    @endif
                </div>
                <p class="mt-2 mb-0" style="color: #374151; line-height: 1.6;">{{ $post->content }}</p>
            </div>
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
                    + Nouvelle
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

        {{-- Suggestions d'utilisateurs à suivre --}}
        @if($suggestions->count() > 0)
        <div class="ss-card p-3">
            <h6 class="fw-bold mb-3" style="color: #1f2937;">Suggestions</h6>

            @foreach($suggestions as $suggestion)
                @php
                    $palette2 = ['#3b6fd4','#10b981','#f59e0b','#ef4444','#8b5cf6','#ec4899'];
                    $sugColor = $palette2[$suggestion->id % count($palette2)];
                    $sugInitials = strtoupper(substr($suggestion->name, 0, 2));
                @endphp
                <div class="d-flex align-items-center gap-2 {{ !$loop->last ? 'mb-3' : '' }}">
                    <div class="ss-avatar text-white flex-shrink-0"
                         style="background: {{ $sugColor }}; width: 34px; height: 34px; font-size: 0.75rem;">
                        {{ $sugInitials }}
                    </div>
                    <div class="flex-grow-1" style="min-width: 0;">
                        <div class="small fw-semibold text-truncate" style="color: #1f2937;">
                            {{ $suggestion->name }}
                        </div>
                    </div>
                    <form action="{{ route('users.follow', $suggestion) }}" method="POST" class="flex-shrink-0">
                        @csrf
                        <button type="submit"
                                class="btn btn-sm"
                                style="background: #fff; border: 1.5px solid #3b6fd4; color: #3b6fd4; border-radius: 0.5rem; font-size: 0.8rem; padding: 0.2rem 0.7rem; white-space: nowrap; font-family: inherit;">
                            + Suivre
                        </button>
                    </form>
                </div>
            @endforeach
        </div>
        @endif

    </div>

</div>
@endsection
