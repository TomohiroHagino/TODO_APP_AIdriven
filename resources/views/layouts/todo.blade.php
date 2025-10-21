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
    <div class="layout">
        <header class="layout__header">
            <div class="flex-between">
                <h1 class="layout__title">📝 Todo App</h1>
                <nav class="nav">
                    @auth
                        <a href="{{ route('todos.index') }}" class="nav__link {{ request()->routeIs('todos.*') ? 'nav__link--active' : '' }}">
                            📋 Todos
                        </a>
                        <a href="{{ route('profile.edit') }}" class="nav__link {{ request()->routeIs('profile.*') ? 'nav__link--active' : '' }}">
                            👤 プロフィール
                        </a>
                        <div style="border-left: 1px solid #e5e7eb; height: 24px; margin: 0 0.5rem;"></div>
                        <span class="text-muted" style="margin-right: 0.5rem;">
                            {{ Auth::user()->name }}
                        </span>
                        <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn btn--secondary btn--small">ログアウト</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="nav__link">ログイン</a>
                        <a href="{{ route('register') }}" class="nav__link">新規登録</a>
                    @endauth
                </nav>
            </div>
        </header>

        <main class="layout__content">
            @yield('content')
        </main>
    </div>
</body>
</html>

