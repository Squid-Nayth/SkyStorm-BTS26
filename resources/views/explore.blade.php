@extends('layouts.app')

@section('content')
<div style="max-width: 900px; margin: 0 auto;">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <h2 class="mb-1 ss-icon-label"><i class="bi bi-compass"></i>Explorer les publications</h2>
            <p class="text-muted mb-0">Recherche simple, profils publics et consultation en mode visiteur.</p>
        </div>
        <form action="{{ route('explore') }}" method="GET" class="d-flex gap-2">
            <input type="text" name="q" value="{{ $search }}" class="form-control" placeholder="Rechercher un post ou un utilisateur">
            <button type="submit" class="btn btn-primary ss-icon-label"><i class="bi bi-search"></i>Rechercher</button>
        </form>
    </div>

    @guest
        <div class="alert alert-info">
            <span class="ss-icon-label"><i class="bi bi-info-circle"></i>En mode visiteur, les publications avec un signalement en attente ou validé sont masquées.</span>
        </div>
    @endguest

    @forelse($posts as $post)
        @include('posts._card', ['post' => $post])
    @empty
        <div class="ss-card p-4 text-center text-muted">Aucune publication trouvée.</div>
    @endforelse
</div>
@endsection
