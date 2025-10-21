@extends('layouts.main')

@push('styles')
    @vite(['resources/css/todos/index.css'])
@endpush

@section('content')
<div class="page-header">
    <h2 class="page-header__title">Todo一覧</h2>
    <a href="{{ route('todos.create') }}" class="btn btn--primary">新規作成</a>
</div>

@if (session('success'))
    <div class="alert alert--success">
        {{ session('success') }}
    </div>
@endif

<!-- フィルタータブ -->
<div class="filter-tabs">
    <a href="{{ route('todos.index') }}" 
       class="filter-tabs__tab {{ is_null($currentStatus) ? 'filter-tabs__tab--active' : '' }}">
        すべて
    </a>
    <a href="{{ route('todos.index', ['status' => 'pending']) }}" 
       class="filter-tabs__tab {{ $currentStatus === 'pending' ? 'filter-tabs__tab--active' : '' }}">
        未完了
    </a>
    <a href="{{ route('todos.index', ['status' => 'done']) }}" 
       class="filter-tabs__tab {{ $currentStatus === 'done' ? 'filter-tabs__tab--active' : '' }}">
        完了
    </a>
</div>

<!-- Todo一覧 -->
@if (count($todos) === 0)
    <div class="todo-list__empty">
        タスクがありません
    </div>
@else
    <ul class="todo-list">
        @foreach ($todos as $todo)
            <li class="todo-item">
                <div class="todo-item__header">
                    <div class="todo-item__title-wrapper">
                        <form method="POST" action="{{ route('todos.toggle', $todo->getId()->getValue()) }}" style="display: inline;">
                            @csrf
                            @method('PATCH')
                            <input type="checkbox" 
                                   class="todo-item__checkbox"
                                   {{ $todo->isDone() ? 'checked' : '' }}
                                   onchange="this.form.submit()">
                        </form>
                        <a href="{{ route('todos.show', $todo->getId()->getValue()) }}" 
                           class="todo-item__title {{ $todo->isDone() ? 'todo-item__title--done' : '' }}">
                            {{ $todo->getTitle()->getValue() }}
                        </a>
                    </div>
                    <span class="todo-item__status {{ $todo->isDone() ? 'todo-item__status--done' : 'todo-item__status--pending' }}">
                        {{ $todo->isDone() ? '完了' : '未完了' }}
                    </span>
                </div>
                
                <div class="todo-item__meta">
                    <span class="todo-item__date">
                        📅 {{ $todo->getCreatedAt()->format('Y年m月d日') }}
                    </span>
                </div>

                <div class="todo-item__actions">
                    <a href="{{ route('todos.show', $todo->getId()->getValue()) }}" class="btn btn--secondary btn--small">詳細</a>
                    <a href="{{ route('todos.edit', $todo->getId()->getValue()) }}" class="btn btn--primary btn--small">編集</a>
                    <form method="POST" action="{{ route('todos.destroy', $todo->getId()->getValue()) }}" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn--danger btn--small" onclick="return confirm('本当に削除しますか？')">削除</button>
                    </form>
                </div>
            </li>
        @endforeach
    </ul>
@endif
@endsection
