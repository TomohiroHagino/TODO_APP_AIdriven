@extends('layouts.app')

@section('title', 'Todo詳細')

@section('content')
<div class="page-header">
    <h1 class="page-header__title">📋 タスク詳細</h1>
</div>

<div class="card">
    <div class="card__header">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <h2 class="card__title">{{ $todo->getTitle()->getValue() }}</h2>
            <span class="badge badge--{{ $todo->isDone() ? 'success' : 'warning' }}">
                {{ $todo->isDone() ? '✓ 完了' : '⏳ 未完了' }}
            </span>
        </div>
    </div>
    
    <div class="card__body">
        <div class="detail-list">
            <div class="detail-list__item">
                <dt class="detail-list__label">タスク名</dt>
                <dd class="detail-list__value">{{ $todo->getTitle()->getValue() }}</dd>
            </div>
            
            <div class="detail-list__item">
                <dt class="detail-list__label">ステータス</dt>
                <dd class="detail-list__value">
                    @if($todo->isDone())
                        <span style="color: #10b981; font-weight: 500;">✓ 完了</span>
                    @else
                        <span style="color: #f59e0b; font-weight: 500;">⏳ 未完了</span>
                    @endif
                </dd>
            </div>
            
            <div class="detail-list__item">
                <dt class="detail-list__label">作成日時</dt>
                <dd class="detail-list__value">{{ $todo->getCreatedAt()->format('Y年m月d日 H:i') }}</dd>
            </div>
            
            <div class="detail-list__item">
                <dt class="detail-list__label">タスクID</dt>
                <dd class="detail-list__value">#{{ $todo->getId() }}</dd>
            </div>
        </div>
    </div>
    
    <div class="card__footer">
        <div class="btn-group">
            <a href="{{ route('todos.index') }}" class="btn btn--outline">
                ← 一覧に戻る
            </a>
            
            <form action="{{ route('todos.toggle', $todo->getId()) }}" method="POST" style="display: inline;">
                @csrf
                <button type="submit" class="btn btn--{{ $todo->isDone() ? 'warning' : 'success' }}">
                    {{ $todo->isDone() ? '未完了に戻す' : '✓ 完了にする' }}
                </button>
            </form>
            
            <a href="{{ route('todos.edit', $todo->getId()) }}" class="btn btn--secondary">
                ✏️ 編集
            </a>
            
            <form action="{{ route('todos.destroy', $todo->getId()) }}" method="POST" style="display: inline;" onsubmit="return confirm('本当に削除しますか？')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn--danger">
                    🗑️ 削除
                </button>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
/* Badge */
.badge {
    display: inline-flex;
    align-items: center;
    padding: 0.375rem 0.875rem;
    border-radius: 9999px;
    font-size: 0.8125rem;
    font-weight: 500;
}

.badge--success {
    background: #d1fae5;
    color: #065f46;
}

.badge--warning {
    background: #fef3c7;
    color: #92400e;
}

/* Detail List */
.detail-list {
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
}

.detail-list__item {
    display: grid;
    grid-template-columns: 150px 1fr;
    gap: 1rem;
    padding-bottom: 1.25rem;
    border-bottom: 1px solid #e5e7eb;
}

.detail-list__item:last-child {
    border-bottom: none;
    padding-bottom: 0;
}

.detail-list__label {
    font-weight: 600;
    color: #374151;
    margin: 0;
}

.detail-list__value {
    color: #111827;
    margin: 0;
}

@media (max-width: 768px) {
    .detail-list__item {
        grid-template-columns: 1fr;
        gap: 0.5rem;
    }
}

.btn--warning {
    background: #f59e0b;
    color: #fff;
}

.btn--warning:hover {
    background: #d97706;
}
</style>
@endpush
@endsection

