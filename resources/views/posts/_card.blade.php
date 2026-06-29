@php
    $isOwn = auth()->check() && $post->user_id === auth()->id();
    $isLiked = auth()->check() ? $post->likes->contains('user_id', auth()->id()) : false;
    $isFavorite = auth()->check() ? $post->favoritedBy->contains('id', auth()->id()) : false;
@endphp

<div class="ss-card p-3 mb-3">
    <div class="d-flex justify-content-between align-items-start gap-3">
        <div class="d-flex gap-3">
            @include('users._avatar', ['user' => $post->user, 'size' => 38])
            <div>
                <div class="fw-semibold" style="color: #1f2937; line-height: 1.3;">
                    <a href="{{ route('users.show', $post->user) }}" class="text-decoration-none" style="color: inherit;">
                        {{ $post->user->name }}
                    </a>
                </div>
                <div class="small" style="color: #9ca3af;">{{ $post->created_at->format('d/m/Y · H:i') }}</div>
            </div>
        </div>

        <div class="d-flex gap-2 align-items-center flex-wrap justify-content-end">
            @if(($post->active_reports_count ?? 0) > 0)
                <span class="badge text-bg-warning ss-icon-label"><i class="bi bi-flag"></i>Signalé</span>
            @endif

            @if($isOwn)
                <a href="{{ route('posts.edit', $post) }}"
                   class="btn btn-sm btn-outline-secondary py-0 px-2 ss-icon-label"
                   style="font-size: 0.75rem; border-radius: 0.4rem;"><i class="bi bi-pencil"></i>Modifier</a>
                <form action="{{ route('posts.destroy', $post) }}" method="POST" onsubmit="return confirm('Supprimer ce post ?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="btn btn-sm btn-outline-danger py-0 px-2 ss-icon-label"
                            style="font-size: 0.75rem; border-radius: 0.4rem;"><i class="bi bi-trash"></i>Supprimer</button>
                </form>
            @endif
        </div>
    </div>

    <p class="mt-3 mb-3" style="color: #374151; line-height: 1.6;">{{ $post->content }}</p>

    <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
        <span class="badge rounded-pill text-bg-light ss-icon-label"><i class="bi bi-hand-thumbs-up"></i>{{ $post->likes_count }} likes</span>
        <span class="badge rounded-pill text-bg-light ss-icon-label"><i class="bi bi-chat-left-text"></i>{{ $post->comments_count }} commentaires</span>
        <span class="badge rounded-pill text-bg-light ss-icon-label"><i class="bi bi-bookmark-heart"></i>{{ $post->favorited_by_count }} favoris</span>

        @auth
            <form action="{{ $isLiked ? route('posts.likes.destroy', $post) : route('posts.likes.store', $post) }}" method="POST">
                @csrf
                @if($isLiked)
                    @method('DELETE')
                @endif
                <button type="submit" class="btn btn-sm {{ $isLiked ? 'btn-primary' : 'btn-outline-primary' }}">
                    <span class="ss-icon-label"><i class="bi {{ $isLiked ? 'bi-hand-thumbs-up-fill' : 'bi-hand-thumbs-up' }}"></i>{{ $isLiked ? 'Retirer le like' : 'Liker' }}</span>
                </button>
            </form>

            <form action="{{ $isFavorite ? route('posts.favorites.destroy', $post) : route('posts.favorites.store', $post) }}" method="POST">
                @csrf
                @if($isFavorite)
                    @method('DELETE')
                @endif
                <button type="submit" class="btn btn-sm {{ $isFavorite ? 'btn-success' : 'btn-outline-success' }}">
                    <span class="ss-icon-label"><i class="bi {{ $isFavorite ? 'bi-bookmark-heart-fill' : 'bi-bookmark-heart' }}"></i>{{ $isFavorite ? 'Retirer des favoris' : 'Ajouter aux favoris' }}</span>
                </button>
            </form>
        @endauth
    </div>

    @if($post->comments->isNotEmpty())
        <div class="mb-3">
            @foreach($post->comments->take(3) as $comment)
                <div class="rounded px-3 py-2 mb-2" style="background: #f9fafb;">
                    <div class="d-flex justify-content-between gap-2">
                        <div class="small">
                            <span class="fw-semibold">{{ $comment->user->name }}</span>
                            <span style="color: #374151;">{{ $comment->content }}</span>
                        </div>
                        @auth
                            @if($comment->user_id === auth()->id() || $post->user_id === auth()->id())
                                <form action="{{ route('comments.destroy', $comment) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-link p-0 text-danger text-decoration-none ss-icon-label"><i class="bi bi-trash"></i>Supprimer</button>
                                </form>
                            @endif
                        @endauth
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    @auth
        <form action="{{ route('posts.comments.store', $post) }}" method="POST" class="mb-2">
            @csrf
            <div class="d-flex gap-2">
                <input type="text" name="content" class="form-control" maxlength="255"
                       placeholder="Ajouter un commentaire...">
                <button type="submit" class="btn btn-outline-secondary ss-icon-label"><i class="bi bi-chat-left-text"></i>Commenter</button>
            </div>
        </form>

        <form action="{{ route('posts.reports.store', $post) }}" method="POST" class="d-flex gap-2 align-items-center">
            @csrf
            <input type="text" name="reason" class="form-control form-control-sm" maxlength="255"
                   placeholder="Raison du signalement">
            <button type="submit" class="btn btn-sm btn-outline-danger ss-icon-label"><i class="bi bi-flag"></i>Signaler</button>
        </form>
    @endauth
</div>
