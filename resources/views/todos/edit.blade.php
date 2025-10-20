@extends('layouts.app')

@section('title', 'Todo編集')

@section('content')
<div class="page-header">
    <h1 class="page-header__title">✏️ タスクを編集</h1>
    <p class="page-header__description">タスクの内容を変更できます</p>
</div>

<div class="card">
    <div class="card__body">
        <form action="{{ route('todos.update', $todo->getId()) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="form-group">
                <label for="title" class="form-label form-label--required">タスク名</label>
                <input 
                    type="text" 
                    id="title" 
                    name="title" 
                    class="form-input {{ $errors->has('title') ? 'form-input--error' : '' }}"
                    value="{{ old('title', $todo->getTitle()->getValue()) }}"
                    autofocus
                >
                @error('title')
                    <p class="form-error">{{ $message }}</p>
                @enderror
                <p class="form-help">255文字以内で入力してください</p>
            </div>
            
            <div class="info-box info-box--info">
                <span class="info-box__icon">ℹ️</span>
                <div class="info-box__content">
                    <p class="info-box__title">現在のステータス</p>
                    <p class="info-box__text">
                        {{ $todo->isDone() ? '✓ 完了' : '⏳ 未完了' }}
                        （ステータスを変更する場合は詳細ページから操作してください）
                    </p>
                </div>
            </div>
            
            <div class="btn-group">
                <button type="submit" class="btn btn--primary btn--large">
                    ✓ 更新する
                </button>
                <a href="{{ route('todos.show', $todo->getId()) }}" class="btn btn--outline btn--large">
                    キャンセル
                </a>
            </div>
        </form>
    </div>
</div>

@push('styles')
<style>
/* Info Box */
.info-box {
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
    padding: 1rem;
    border-radius: 8px;
    margin-bottom: 1.5rem;
}

.info-box--info {
    background: #eff6ff;
    border: 1px solid #93c5fd;
}

.info-box__icon {
    font-size: 1.25rem;
    flex-shrink: 0;
}

.info-box__content {
    flex: 1;
}

.info-box__title {
    font-weight: 600;
    color: #1e40af;
    margin: 0 0 0.25rem 0;
    font-size: 0.875rem;
}

.info-box__text {
    color: #1e40af;
    margin: 0;
    font-size: 0.875rem;
}
</style>
@endpush
@endsection

