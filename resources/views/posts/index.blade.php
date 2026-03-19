@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2>Mes posts</h2>
                <a href="{{ route('posts.create') }}" class="btn btn-primary">+ Nouveau post</a>
            </div>

            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif

            @forelse ($posts as $post)
                <div class="card mb-3">
                    <div class="card-body">
                        <p class="card-text">{{ $post->content }}</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">{{ $post->created_at->format('d/m/Y H:i') }}</small>
                            <div class="d-flex gap-2">
                                <a href="{{ route('posts.edit', $post) }}" class="btn btn-sm btn-warning">Modifier</a>
                                <form action="{{ route('posts.destroy', $post) }}" method="POST"
                                      onsubmit="return confirm('Supprimer ce post ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Supprimer</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="alert alert-info">Aucun post pour l'instant.</div>
            @endforelse

        </div>
    </div>
</div>
@endsection
