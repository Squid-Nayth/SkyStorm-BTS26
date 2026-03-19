@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2>Mes notes privées</h2>
                <a href="{{ route('notes.create') }}" class="btn btn-primary">Nouvelle note</a>
            </div>

            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif

            @forelse ($notes as $note)
                <div class="card mb-3">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <strong>{{ $note->titre }}</strong>
                        <small class="text-muted">{{ $note->created_at->format('d/m/Y H:i') }}</small>
                    </div>
                    <div class="card-body">
                        <p class="card-text">{{ $note->contenu }}</p>
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
                </div>
            @empty
                <div class="alert alert-info">Vous n'avez pas encore de notes.</div>
            @endforelse

        </div>
    </div>
</div>
@endsection
