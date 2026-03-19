@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">

            {{-- Message de bienvenue --}}
            <div class="card mb-4">
                <div class="card-header">{{ __('Dashboard') }}</div>
                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    Bienvenue, <strong>{{ auth()->user()->name }}</strong> !
                </div>
            </div>

            {{-- Mes notes privées --}}
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Mes notes privées</span>
                    <a href="{{ route('notes.create') }}" class="btn btn-sm btn-primary">+ Nouvelle note</a>
                </div>
                <div class="card-body">
                    @forelse ($notes as $note)
                        <div class="border rounded p-3 mb-2">
                            <div class="d-flex justify-content-between">
                                <strong>{{ $note->titre }}</strong>
                                <small class="text-muted">{{ $note->created_at->format('d/m/Y') }}</small>
                            </div>
                            <p class="mb-1 mt-1">{{ $note->contenu }}</p>
                            <div class="d-flex gap-2">
                                <a href="{{ route('notes.edit', $note) }}" class="btn btn-sm btn-warning">Modifier</a>
                                <form action="{{ route('notes.destroy', $note) }}" method="POST"
                                      onsubmit="return confirm('Supprimer cette note ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Supprimer</button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted mb-0">Aucune note pour l'instant.</p>
                    @endforelse
                </div>
            </div>

            {{-- Mes posts --}}
            <div id="posts" class="card mb-4">
                <div class="card-header">Mes posts</div>
                <div class="card-body">

                    {{-- Formulaire d'ajout --}}
                    <form id="nouveau-post" action="{{ route('posts.store') }}" method="POST" class="mb-3">
                        @csrf
                        <div class="mb-2">
                            <textarea name="content" class="form-control @error('content') is-invalid @enderror"
                                      rows="2" placeholder="Écrire un post..." required>{{ old('content') }}</textarea>
                            @error('content')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm">Publier</button>
                    </form>

                    <hr>

                    @forelse ($posts as $post)
                        <div class="border rounded p-3 mb-2">
                            <p class="mb-1">{{ $post->content }}</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">{{ $post->created_at->format('d/m/Y H:i') }}</small>
                                <form action="{{ route('posts.destroy', $post) }}" method="POST"
                                      onsubmit="return confirm('Supprimer ce post ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Supprimer</button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted mb-0">Aucun post pour l'instant.</p>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
