<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'SkyStorm') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon-skystorm.png') }}">
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

        .ss-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #d1d5db;
            flex-shrink: 0;
        }
        .ss-nav-link.active .ss-dot { background: #3b6fd4; }

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
                <span class="ss-dot"></span> Accueil
            </a>
            <a href="{{ route('notes.index') }}" class="ss-nav-link {{ request()->is('notes*') ? 'active' : '' }}">
                <span class="ss-dot"></span> Notes
            </a>
            <a href="{{ route('posts.index') }}" class="ss-nav-link {{ request()->is('posts*') ? 'active' : '' }}">
                <span class="ss-dot"></span> Posts
            </a>

            <hr class="my-3" style="border-color: #e5e7eb;">

            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="ss-nav-link border-0 bg-transparent w-100 text-start" style="cursor: pointer; font-family: inherit;">
                    <span class="ss-dot"></span> Se déconnecter
                </button>
            </form>
        </nav>
    </aside>

    {{-- Zone principale --}}
    <div class="flex-grow-1 d-flex flex-column" style="min-width: 0; overflow: hidden;">

        {{-- Barre supérieure --}}
        <header class="ss-topbar d-flex align-items-center gap-3 px-4">
            <input type="text" placeholder="Rechercher..." disabled
                   class="form-control"
                   style="max-width: 320px; border-radius: 9999px; background: #f9fafb; border-color: #e5e7eb; font-family: inherit;">
            <div class="ms-auto d-flex align-items-center gap-2">
                @php $initials = strtoupper(substr(auth()->user()->name, 0, 2)); @endphp
                <div class="ss-avatar text-white" style="background: #3b6fd4;">{{ $initials }}</div>
            </div>
        </header>

        {{-- Contenu de la page --}}
        <main class="flex-grow-1 p-4" style="overflow-y: auto;">
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
                @if (Route::has('login'))
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                    </li>
                @endif
                @if (Route::has('register'))
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
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
