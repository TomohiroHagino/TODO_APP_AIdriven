@extends('layouts.app')

@section('title', 'Todo一覧')

@push('styles')
<style>
    {!! file_get_contents(resource_path('css/todos/common.css')) !!}
    {!! file_get_contents(resource_path('css/todos/index.css')) !!}
</style>
@endpush

@section('content')
<div class="page-header">
    <h1 class="page-header__title">📝 Todo一覧</h1>
    <p class="page-header__description">タスクを管理しましょう</p>
</div>

<!-- フィルターバー -->
<div class="filter-bar">
    <span class="filter-bar__label">絞り込み:</span>
    <div class="filter-bar__buttons">
        <a href="{{ route('todos.index') }}" 
           class="filter-bar__button {{ is_null($currentStatus) ? 'filter-bar__button--active' : '' }}">
            全て
        </a>
        <a href="{{ route('todos.index', ['status' => 'pending']) }}" 
           class="filter-bar__button {{ $currentStatus === 'pending' ? 'filter-bar__button--active' : '' }}">
            未完了
        </a>
        <a href="{{ route('todos.index', ['status' => 'done']) }}" 
           class="filter-bar__button {{ $currentStatus === 'done' ? 'filter-bar__button--active' : '' }}">
            完了
        </a>
    </div>
</div>

<!-- Todo一覧カード -->
<div class="card">
    <div class="card__header">
        <h2 class="card__title">
            タスク一覧
            <span style="color: #6b7280; font-size: 0.875rem; font-weight: 400;">
                ({{ count($todos) }}件)
            </span>
        </h2>
    </div>
    
    <div class="card__body">
        @if(count($todos) > 0)
            <div class="todo-list">
                @foreach($todos as $todo)
                    <div class="todo-item {{ $todo->isDone() ? 'todo-item--completed' : '' }}">
                        <div class="todo-item__content">
                            <div class="todo-item__status">
                                <form action="{{ route('todos.toggle', $todo->getId()) }}" method="POST" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="todo-item__checkbox" title="{{ $todo->isDone() ? '未完了にする' : '完了にする' }}">
                                        @if($todo->isDone())
                                            <span class="todo-item__checkbox-icon">✓</span>
                                        @else
                                            <span class="todo-item__checkbox-icon todo-item__checkbox-icon--empty"></span>
                                        @endif
                                    </button>
                                </form>
                            </div>
                            
                            <div class="todo-item__details">
                                <h3 class="todo-item__title">
                                    <a href="{{ route('todos.show', $todo->getId()) }}" class="todo-item__title-link">
                                        {{ $todo->getTitle()->getValue() }}
                                    </a>
                                </h3>
                                <p class="todo-item__date">
                                    作成日: {{ $todo->getCreatedAt()->format('Y年m月d日 H:i') }}
                                </p>
                            </div>
                        </div>
                        
                        <div class="todo-item__actions">
                            <a href="{{ route('todos.show', $todo->getId()) }}" class="btn btn--outline btn--small">
                                詳細
                            </a>
                            <a href="{{ route('todos.edit', $todo->getId()) }}" class="btn btn--secondary btn--small">
                                編集
                            </a>
                            <form action="{{ route('todos.destroy', $todo->getId()) }}" method="POST" style="display: inline;" onsubmit="return confirm('本当に削除しますか？')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn--danger btn--small">
                                    削除
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="empty-state">
                <div class="empty-state__icon">📭</div>
                <h3 class="empty-state__title">タスクがありません</h3>
                <p class="empty-state__message">
                    @if($currentStatus === 'done')
                        完了したタスクはまだありません
                    @elseif($currentStatus === 'pending')
                        未完了のタスクはありません
                    @else
                        新しいタスクを作成してください
                    @endif
                </p>
                <a href="{{ route('todos.create') }}" class="btn btn--primary">
                    ➕ 新規作成
                </a>
            </div>
        @endif
    </div>
    
    @if(count($todos) > 0)
        <div class="card__footer">
            <div class="btn-group btn-group--right">
                <a href="{{ route('todos.create') }}" class="btn btn--primary">
                    ➕ 新規作成
                </a>
            </div>
        </div>
    @endif
</div>
@endsection

