<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TodoController;

// ホーム画面はTodo一覧にリダイレクト
Route::get('/', function () {
    return redirect()->route('todos.index');
});

// Todoのリソースルート
Route::resource('todos', TodoController::class);

// Todoの完了/未完了切り替え（カスタムルート）
Route::post('todos/{id}/toggle', [TodoController::class, 'toggle'])->name('todos.toggle');
