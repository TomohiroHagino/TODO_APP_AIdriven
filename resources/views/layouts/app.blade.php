<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Todoアプリ')</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;500;700&display=swap" rel="stylesheet">
    
    <!-- 一時的: CSSを直接読み込み（本番ではViteを使用） -->
    <style>
        {!! file_get_contents(resource_path('css/app.css')) !!}
    </style>
    
    @stack('styles')
</head>
<body class="app">
    <!-- ヘッダー -->
    <header class="header">
        <div class="header__container">
            <h1 class="header__logo">
                <a href="{{ route('todos.index') }}" class="header__logo-link">
                    📝 Todo App
                </a>
            </h1>
            
            <nav class="header__nav">
                <a href="{{ route('todos.index') }}" class="header__nav-link {{ request()->routeIs('todos.index') ? 'header__nav-link--active' : '' }}">
                    一覧
                </a>
                <a href="{{ route('todos.create') }}" class="header__nav-link {{ request()->routeIs('todos.create') ? 'header__nav-link--active' : '' }}">
                    新規作成
                </a>
            </nav>
        </div>
    </header>

    <!-- メインコンテンツ -->
    <main class="main">
        <div class="main__container">
            <!-- フラッシュメッセージ -->
            @if (session('success'))
                <div class="alert alert--success">
                    <span class="alert__icon">✓</span>
                    <span class="alert__message">{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert--error">
                    <span class="alert__icon">✕</span>
                    <span class="alert__message">{{ session('error') }}</span>
                </div>
            @endif

            <!-- バリデーションエラー -->
            @if ($errors->any())
                <div class="alert alert--error">
                    <span class="alert__icon">✕</span>
                    <div class="alert__message">
                        <ul class="alert__list">
                            @foreach ($errors->all() as $error)
                                <li class="alert__list-item">{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <!-- ページコンテンツ -->
            @yield('content')
        </div>
    </main>

    <!-- フッター -->
    <footer class="footer">
        <div class="footer__container">
            <p class="footer__text">
                &copy; {{ date('Y') }} Todo App. All rights reserved.
            </p>
        </div>
    </footer>

    <!-- JavaScript -->
    @stack('scripts')
</body>
</html>

