@extends('layouts.app')

@section('content')
<div style="max-width: 900px; margin: 0 auto;">
    <div class="mb-4">
        <h2 class="mb-1">Favoris de {{ $user->name }}</h2>
        <p class="text-muted mb-0">Liste publique de publications enregistrées.</p>
    </div>

    @forelse($posts as $post)
        @include('posts._card', ['post' => $post])
    @empty
        <div class="ss-card p-4 text-center text-muted">Cette liste de favoris est vide.</div>
    @endforelse
</div>
@endsection
