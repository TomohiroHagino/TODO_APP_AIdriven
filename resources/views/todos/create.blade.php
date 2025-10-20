@extends('layouts.app')

@section('title', 'Todo作成')

@section('content')
<div class="page-header">
    <h1 class="page-header__title">➕ 新しいタスクを作成</h1>
    <p class="page-header__description">タスクの詳細を入力してください</p>
</div>

<div class="card">
    <div class="card__body">
        <form action="{{ route('todos.store') }}" method="POST">
            @csrf
            
            <div class="form-group">
                <label for="title" class="form-label form-label--required">タスク名</label>
                <input 
                    type="text" 
                    id="title" 
                    name="title" 
                    class="form-input {{ $errors->has('title') ? 'form-input--error' : '' }}"
                    placeholder="例: 買い物に行く"
                    value="{{ old('title') }}"
                    autofocus
                >
                @error('title')
                    <p class="form-error">{{ $message }}</p>
                @enderror
                <p class="form-help">255文字以内で入力してください</p>
            </div>
            
            <div class="btn-group">
                <button type="submit" class="btn btn--primary btn--large">
                    ✓ 作成する
                </button>
                <a href="{{ route('todos.index') }}" class="btn btn--outline btn--large">
                    キャンセル
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

