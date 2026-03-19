@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Modifier la note</div>
                <div class="card-body">
                    <form action="{{ route('notes.update', $note) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="titre" class="form-label">Titre</label>
                            <input type="text" name="titre" id="titre"
                                   class="form-control @error('titre') is-invalid @enderror"
                                   value="{{ old('titre', $note->titre) }}" required>
                            @error('titre')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="contenu" class="form-label">Contenu</label>
                            <textarea name="contenu" id="contenu" rows="6"
                                      class="form-control @error('contenu') is-invalid @enderror"
                                      required>{{ old('contenu', $note->contenu) }}</textarea>
                            @error('contenu')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Mettre à jour</button>
                            <a href="{{ route('notes.index') }}" class="btn btn-secondary">Annuler</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
