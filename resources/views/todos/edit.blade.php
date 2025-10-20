@extends('layouts.app')

@section('title', 'Todo編集')

@push('styles')
<style>
    {!! file_get_contents(resource_path('css/todos/common.css')) !!}
    {!! file_get_contents(resource_path('css/todos/edit.css')) !!}
</style>
@endpush

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
@endsection

