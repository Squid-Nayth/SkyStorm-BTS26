@php
    $avatarSize = $size ?? 38;
    $initials = strtoupper(substr($user->name, 0, 2));
@endphp

@if($user->avatarUrl())
    <img src="{{ $user->avatarUrl() }}"
         alt="{{ $user->name }}"
         style="width: {{ $avatarSize }}px; height: {{ $avatarSize }}px; border-radius: 50%; object-fit: cover; flex-shrink: 0;">
@else
    <div class="ss-avatar text-white"
         style="width: {{ $avatarSize }}px; height: {{ $avatarSize }}px; background: #3b6fd4; font-size: {{ max(12, intval($avatarSize / 2.5)) }}px;">
        {{ $initials }}
    </div>
@endif
