@extends('layouts.main')

@push('styles')
    @vite(['resources/css/todos/show.css'])
@endpush

@section('content')
<div class="todo-detail">
    <a href="{{ route('todos.index') }}" class="todo-detail__back">
        ← 一覧に戻る
    </a>

    <div class="todo-detail__header">
        <div class="todo-detail__title-wrapper">
            <h2 class="todo-detail__title {{ $todo->isDone() ? 'todo-detail__title--done' : '' }}">
                {{ $todo->getTitle()->getValue() }}
            </h2>
            <span class="todo-detail__status {{ $todo->isDone() ? 'todo-detail__status--done' : 'todo-detail__status--pending' }}">
                {{ $todo->isDone() ? '✓ 完了' : '○ 未完了' }}
            </span>
        </div>
    </div>

    <div class="todo-detail__info">
        <div class="todo-detail__info-row">
            <span class="todo-detail__info-label">ID</span>
            <span class="todo-detail__info-value">{{ $todo->getId()->getValue() }}</span>
        </div>
        <div class="todo-detail__info-row">
            <span class="todo-detail__info-label">タイトル</span>
            <span class="todo-detail__info-value">{{ $todo->getTitle()->getValue() }}</span>
        </div>
        <div class="todo-detail__info-row">
            <span class="todo-detail__info-label">ステータス</span>
            <span class="todo-detail__info-value">{{ $todo->isDone() ? '完了' : '未完了' }}</span>
        </div>
        <div class="todo-detail__info-row">
            <span class="todo-detail__info-label">作成日時</span>
            <span class="todo-detail__info-value">{{ $todo->getCreatedAt()->format('Y年m月d日 H:i') }}</span>
        </div>
    </div>

    <div class="todo-detail__actions">
        <form method="POST" action="{{ route('todos.toggle', $todo->getId()->getValue()) }}" style="display: inline;">
            @csrf
            @method('PATCH')
            <button type="submit" class="btn {{ $todo->isDone() ? 'btn--secondary' : 'btn--success' }}">
                {{ $todo->isDone() ? '未完了に戻す' : '完了にする' }}
            </button>
        </form>
        <a href="{{ route('todos.edit', $todo->getId()->getValue()) }}" class="btn btn--primary">編集</a>
        <form method="POST" action="{{ route('todos.destroy', $todo->getId()->getValue()) }}" style="display: inline;">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn--danger" onclick="return confirm('本当に削除しますか？')">削除</button>
        </form>
    </div>
</div>
@endsection
