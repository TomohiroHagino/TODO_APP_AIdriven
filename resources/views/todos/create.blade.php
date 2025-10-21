@extends('layouts.main')

@section('content')
<div class="todo-form">
    <a href="{{ route('todos.index') }}" class="todo-form__back">
        ← 一覧に戻る
    </a>

    <div class="todo-form__header">
        <h2 class="todo-form__title">Todo作成</h2>
    </div>

    <form method="POST" action="{{ route('todos.store') }}" class="todo-form__body">
        @csrf

        <div class="form-group">
            <label for="title" class="form-group__label">タイトル</label>
            <input type="text" 
                   id="title" 
                   name="title" 
                   class="form-group__input" 
                   value="{{ old('title') }}"
                   required
                   autofocus>
            @error('title')
                <div class="form-group__error">{{ $message }}</div>
            @enderror
        </div>

        <div class="todo-form__actions">
            <a href="{{ route('todos.index') }}" class="btn btn--secondary">キャンセル</a>
            <button type="submit" class="btn btn--primary">作成</button>
        </div>
    </form>
</div>
@endsection
