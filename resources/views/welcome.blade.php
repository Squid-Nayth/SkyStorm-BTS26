<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'SkyStorm') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon-skystorm.png') }}">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { display: flex; flex-direction: column; height: 100vh; font-family: sans-serif; }

        nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 2rem;
            height: 60px;
            background: #fff;
            box-shadow: 0 1px 4px rgba(0,0,0,0.1);
            flex-shrink: 0;
        }

        .brand {
            font-size: 1.5rem;
            font-weight: 700;
            text-decoration: none;
        }
        .brand .sky   { color: #1b1b18; }
        .brand .storm { color: #3b6fd4; }

        .nav-links { display: flex; gap: 1rem; align-items: center; }
        .nav-links a {
            text-decoration: none;
            font-size: 0.9rem;
            padding: 0.4rem 1rem;
            border-radius: 4px;
            border: 1px solid transparent;
            color: #1b1b18;
            transition: border-color 0.15s;
        }
        .nav-links a:hover { border-color: #ccc; }
        .nav-links a.btn-primary {
            background: #3b6fd4;
            color: #fff;
            border-color: #3b6fd4;
        }
        .nav-links a.btn-primary:hover { background: #2f5bb8; border-color: #2f5bb8; }

        .hero {
            flex: 1;
            overflow: hidden;
        }
        .hero img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            background: #f8f8f5;
        }
    </style>
</head>
<body>

    <nav>
        <a href="{{ url('/') }}" class="brand">
            <span class="sky">Sky</span><span class="storm">Storm</span>
        </a>
        <div class="nav-links">
            @auth
                <a href="{{ url('/home') }}" class="btn-primary">Dashboard</a>
            @else
                @if (Route::has('login'))
                    <a href="{{ route('login') }}">Connexion</a>
                @endif
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="btn-primary">S'inscrire</a>
                @endif
            @endauth
        </div>
    </nav>

    <div class="hero">
        <img src="{{ asset('banner-skystorm.png') }}" alt="SkyStorm">
    </div>

</body>
</html>
