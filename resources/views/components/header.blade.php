<header class="header">
    <div class="header__container">
        <h1 class="header__logo">
            <a href="{{ route('todos.index') }}" class="header__logo-link">📝 Todo App</a>
        </h1>

        <!-- ハンバーガーメニューボタン（モバイル） -->
        <button class="header__menu-toggle" id="menuToggle" aria-label="メニューを開く">
            <span class="header__menu-icon"></span>
            <span class="header__menu-icon"></span>
            <span class="header__menu-icon"></span>
        </button>

        <!-- ナビゲーション -->
        <nav class="header__nav" id="headerNav">
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

