<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'SkyStorm') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon-skystorm.png') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <style>
        body { background-color: #fff; font-family: 'Nunito', sans-serif; }

        .ss-sidebar {
            width: 220px;
            min-width: 220px;
            height: 100vh;
            position: sticky;
            top: 0;
            overflow-y: auto;
            background: #fff;
            border-right: 1px solid #e5e7eb;
        }

        .ss-nav-link {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            padding: 0.55rem 0.75rem;
            border-radius: 0.5rem;
            text-decoration: none;
            color: #374151;
            margin-bottom: 0.2rem;
            transition: background 0.15s;
            font-size: 0.95rem;
        }
        .ss-nav-link:hover { background: #f3f4f6; color: #374151; }
        .ss-nav-link.active { background: #eff6ff; color: #3b6fd4; font-weight: 700; }

        .ss-nav-icon {
            width: 18px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 0.95rem;
            color: #6b7280;
        }
        .ss-nav-link.active .ss-nav-icon { color: #3b6fd4; }

        .ss-topbar {
            position: sticky;
            top: 0;
            z-index: 100;
            background: #fff;
            border-bottom: 1px solid #e5e7eb;
            height: 64px;
        }

        .ss-avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.8rem;
            flex-shrink: 0;
        }

        .ss-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 0.75rem;
        }

        .ss-icon-label {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
        }
    </style>
</head>
<body>
<div id="app">

@auth
{{-- Layout authentifié : sidebar + contenu --}}
<div class="d-flex">

    {{-- Sidebar gauche --}}
    <aside class="ss-sidebar">
        <div class="px-4 pt-4 pb-2">
            <a href="{{ route('home') }}" class="text-decoration-none fw-bold" style="font-size: 1.4rem; line-height: 1;">
                <span style="color: #1b1b18;">Sky</span><span style="color: #3b6fd4;">Storm</span>
            </a>
        </div>

        <nav class="px-3 py-2">
            <a href="{{ route('home') }}" class="ss-nav-link {{ request()->is('home') ? 'active' : '' }}">
                <span class="ss-nav-icon"><i class="bi bi-house-door"></i></span> Accueil
            </a>
            <a href="{{ route('explore') }}" class="ss-nav-link {{ request()->is('explore') ? 'active' : '' }}">
                <span class="ss-nav-icon"><i class="bi bi-compass"></i></span> Explorer
            </a>
            <a href="{{ route('members.index') }}" class="ss-nav-link {{ request()->is('members') ? 'active' : '' }}">
                <span class="ss-nav-icon"><i class="bi bi-people"></i></span> Membres
            </a>
            <a href="{{ route('notes.index') }}" class="ss-nav-link {{ request()->is('notes*') ? 'active' : '' }}">
                <span class="ss-nav-icon"><i class="bi bi-journal-text"></i></span> Notes
            </a>
            <a href="{{ route('posts.index') }}" class="ss-nav-link {{ request()->is('posts*') ? 'active' : '' }}">
                <span class="ss-nav-icon"><i class="bi bi-grid-3x3-gap"></i></span> Posts
            </a>
            <a href="{{ route('favorites.index') }}" class="ss-nav-link {{ request()->is('favorites') ? 'active' : '' }}">
                <span class="ss-nav-icon"><i class="bi bi-bookmark-heart"></i></span> Favoris
            </a>
            <a href="{{ route('messages.index') }}" class="ss-nav-link {{ request()->is('messages*') ? 'active' : '' }}">
                <span class="ss-nav-icon"><i class="bi bi-chat-dots"></i></span> Messages
                @if(auth()->user()->unreadMessagesCount() > 0)
                    <span class="badge rounded-pill text-bg-danger ms-auto">{{ auth()->user()->unreadMessagesCount() }}</span>
                @endif
            </a>
            <a href="{{ route('users.show', auth()->user()) }}" class="ss-nav-link {{ request()->is('users/' . auth()->id()) ? 'active' : '' }}">
                <span class="ss-nav-icon"><i class="bi bi-person-circle"></i></span> Mon profil
            </a>
            <a href="{{ route('profile.edit') }}" class="ss-nav-link {{ request()->is('profile/edit') ? 'active' : '' }}">
                <span class="ss-nav-icon"><i class="bi bi-sliders"></i></span> Réglages profil
            </a>
            @if(auth()->user()->is_admin)
                <a href="{{ route('admin.reports.index') }}" class="ss-nav-link {{ request()->is('admin/reports') ? 'active' : '' }}">
                    <span class="ss-nav-icon"><i class="bi bi-shield-exclamation"></i></span> Signalements
                </a>
            @endif

            <hr class="my-3" style="border-color: #e5e7eb;">

            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="ss-nav-link border-0 bg-transparent w-100 text-start" style="cursor: pointer; font-family: inherit;">
                    <span class="ss-nav-icon"><i class="bi bi-box-arrow-right"></i></span> Se déconnecter
                </button>
            </form>
        </nav>
    </aside>

    {{-- Zone principale --}}
    <div class="flex-grow-1 d-flex flex-column" style="min-width: 0; overflow: hidden;">

        {{-- Barre supérieure --}}
        <header class="ss-topbar d-flex align-items-center gap-3 px-4">
            <form action="{{ route('explore') }}" method="GET" class="w-100" style="max-width: 320px;">
                <input type="text" name="q" placeholder="Rechercher..."
                       value="{{ request('q') }}"
                       class="form-control"
                       style="border-radius: 9999px; background: #f9fafb; border-color: #e5e7eb; font-family: inherit;">
            </form>
            <div class="ms-auto d-flex align-items-center gap-2">
                @if(auth()->user()->unreadMessagesCount() > 0)
                    <a href="{{ route('messages.index') }}" class="text-decoration-none">
                        <span class="badge rounded-pill text-bg-danger ss-icon-label"><i class="bi bi-envelope"></i>{{ auth()->user()->unreadMessagesCount() }} message(s)</span>
                    </a>
                @endif
                @include('users._avatar', ['user' => auth()->user(), 'size' => 38])
            </div>
        </header>

        {{-- Contenu de la page --}}
        <main class="flex-grow-1 p-4" style="overflow-y: auto;">
            @if (session('status'))
                <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
                    {{ session('status') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>

    </div>
</div>

@else
{{-- Layout invité --}}
<nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="{{ url('/') }}">
            <span style="color: #1b1b18;">Sky</span><span style="color: #3b6fd4;">Storm</span>
        </a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link ss-icon-label" href="{{ route('explore') }}"><i class="bi bi-compass"></i>Explorer</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link ss-icon-label" href="{{ route('members.index') }}"><i class="bi bi-people"></i>Membres</a>
                </li>
                @if (Route::has('login'))
                    <li class="nav-item">
                        <a class="nav-link ss-icon-label" href="{{ route('login') }}"><i class="bi bi-box-arrow-in-right"></i>{{ __('Login') }}</a>
                    </li>
                @endif
                @if (Route::has('register'))
                    <li class="nav-item">
                        <a class="nav-link ss-icon-label" href="{{ route('register') }}"><i class="bi bi-person-plus"></i>{{ __('Register') }}</a>
                    </li>
                @endif
            </ul>
        </div>
    </div>
</nav>
<main class="py-4">
    @yield('content')
</main>
@endauth

</div>
</body>
</html>
