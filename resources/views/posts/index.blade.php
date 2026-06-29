@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="ss-icon-label mb-0"><i class="bi bi-grid-3x3-gap"></i>Mes posts</h2>
                <a href="{{ route('posts.create') }}" class="btn btn-primary ss-icon-label"><i class="bi bi-plus-lg"></i>Nouveau post</a>
            </div>

            @forelse ($posts as $post)
                @include('posts._card', ['post' => $post])
            @empty
                <div class="alert alert-info">Aucun post pour l'instant.</div>
            @endforelse

        </div>
    </div>
</div>
@endsection
