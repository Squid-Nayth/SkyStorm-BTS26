@extends('layouts.app')

@section('content')
<div style="max-width: 900px; margin: 0 auto;">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
        <div>
            <h2 class="mb-1 ss-icon-label"><i class="bi bi-bookmark-heart"></i>Mes favoris</h2>
            <p class="text-muted mb-0">Maximum 50 publications. Votre liste est actuellement
                <strong>{{ $user->favorite_posts_public ? 'publique' : 'privée' }}</strong>.
            </p>
        </div>

        <form action="{{ route('favorites.visibility') }}" method="POST" class="ss-card p-3">
            @csrf
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="favorite_posts_public" name="favorite_posts_public" value="1"
                       {{ $user->favorite_posts_public ? 'checked' : '' }}>
                <label class="form-check-label" for="favorite_posts_public">Rendre ma liste publique</label>
            </div>
            <button type="submit" class="btn btn-sm btn-primary mt-2 ss-icon-label"><i class="bi bi-save"></i>Enregistrer</button>
        </form>
    </div>

    @if($user->favorite_posts_public)
        <div class="alert alert-success">
            <span class="ss-icon-label"><i class="bi bi-unlock"></i>Votre liste publique est consultable ici :</span>
            <a href="{{ route('favorites.show', $user) }}">{{ route('favorites.show', $user) }}</a>
        </div>
    @endif

    @forelse($posts as $post)
        @include('posts._card', ['post' => $post])
    @empty
        <div class="ss-card p-4 text-center text-muted">Aucun favori pour le moment.</div>
    @endforelse
</div>
@endsection
