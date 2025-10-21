@extends('layouts.main')

@push('styles')
    @vite(['resources/css/todos/edit.css'])
@endpush

@section('content')
<div class="todo-form">
    <a href="{{ route('todos.show', $todo->getId()->getValue()) }}" class="todo-form__back">
        ← 詳細に戻る
    </a>

    <div class="todo-form__header">
        <h2 class="todo-form__title">Todo編集</h2>
    </div>

    <form method="POST" action="{{ route('todos.update', $todo->getId()->getValue()) }}" class="todo-form__body">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="title" class="form-group__label">タイトル</label>
            <input type="text" 
                   id="title" 
                   name="title" 
                   class="form-group__input" 
                   value="{{ old('title', $todo->getTitle()->getValue()) }}"
                   required
                   autofocus>
            @error('title')
                <div class="form-group__error">{{ $message }}</div>
            @enderror
        </div>

        <div class="checkbox-group">
            <label for="is_done" class="checkbox-group__label">
                <input type="checkbox" 
                       id="is_done" 
                       name="is_done" 
                       class="checkbox-group__input"
                       value="1"
                       {{ old('is_done', $todo->isDone()) ? 'checked' : '' }}>
                <span class="checkbox-group__text">完了済み</span>
            </label>
        </div>

        <div class="todo-form__actions">
            <a href="{{ route('todos.show', $todo->getId()->getValue()) }}" class="btn btn--secondary">キャンセル</a>
            <button type="submit" class="btn btn--primary">更新</button>
        </div>
    </form>
</div>
@endsection
