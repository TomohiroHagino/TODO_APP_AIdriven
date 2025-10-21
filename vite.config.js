import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                // Tailwind CSS（認証ページ用）
                'resources/css/app.css',
                'resources/css/app-bem.css',

                // 共通CSS
                'resources/css/reset.css',
                'resources/css/common.css',
                
                // Todoページ個別CSS
                'resources/css/todos/index.css',
                'resources/css/todos/show.css',
                'resources/css/todos/edit.css',
                
                // JavaScript
                'resources/js/app.js',
                'resources/js/common.js'
            ],
            refresh: true,
        }),
    ],
});
