<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Todo App' }}</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="header__container">
            <h1 class="header__logo">
                <a href="{{ route('todos.index') }}" class="header__logo-link">📝 Todo App</a>
            </h1>
            <nav class="header__nav">
                @auth
                    <a href="{{ route('todos.index') }}" class="header__nav-link {{ request()->routeIs('todos.*') ? 'header__nav-link--active' : '' }}">
                        📋 Todos
                    </a>
                    <a href="{{ route('profile.edit') }}" class="header__nav-link {{ request()->routeIs('profile.*') ? 'header__nav-link--active' : '' }}">
                        👤 プロフィール
                    </a>
                    <div class="header__divider"></div>
                    <span class="header__user">{{ Auth::user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn--secondary btn--small">ログアウト</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="header__nav-link">ログイン</a>
                    <a href="{{ route('register') }}" class="header__nav-link">新規登録</a>
                @endauth
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <div class="layout">
        <main class="layout__content">
            @yield('content')
        </main>
    </div>
</body>
</html>

