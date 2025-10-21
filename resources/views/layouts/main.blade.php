<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Todo App' }}</title>
    
    <!-- 共通CSS（全ページで使用） -->
    @vite(['resources/css/app.css', 'resources/css/app-bem.css'])
    
    <!-- ページ固有CSS -->
    @stack('styles')
    
    <!-- JavaScript -->
    @vite(['resources/js/app.js', 'resources/js/common.js'])
</head>
<body class="layout-with-grid">
    <!-- Header Component -->
    <x-header />

    <!-- Main Content -->
    <div class="layout">
        <main class="layout__content">
            @yield('content')
        </main>
    </div>
</body>
</html>

