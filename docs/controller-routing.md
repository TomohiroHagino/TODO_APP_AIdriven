# コントローラーとルーティング設計書

## 概要

このドキュメントは、Todoアプリケーションのコントローラーとルーティングについて説明します。

## アーキテクチャ

```
HTTP Request
    ↓
Route (web.php)
    ↓
TodoController
    ↓
Application Service (UseCase)
    ↓
Domain Layer
    ↓
Infrastructure Layer (Repository)
    ↓
Database
```

## ルーティング一覧

### リソースルート

| メソッド | URI | アクション | 名前 | 説明 |
|---------|-----|----------|------|------|
| GET | /todos | index | todos.index | Todo一覧表示 |
| GET | /todos/create | create | todos.create | 新規作成フォーム |
| POST | /todos | store | todos.store | Todo保存 |
| GET | /todos/{id} | show | todos.show | Todo詳細表示 |
| GET | /todos/{id}/edit | edit | todos.edit | 編集フォーム |
| PUT/PATCH | /todos/{id} | update | todos.update | Todo更新 |
| DELETE | /todos/{id} | destroy | todos.destroy | Todo削除 |

### カスタムルート

| メソッド | URI | アクション | 名前 | 説明 |
|---------|-----|----------|------|------|
| POST | /todos/{id}/toggle | toggle | todos.toggle | 完了/未完了切替 |

## TodoController

### コンストラクタインジェクション

以下の6つのアプリケーションサービスをDIコンテナから注入：

```php
- ListTodosService          // 一覧取得
- CreateTodoService         // 新規作成
- GetTodoDetailService      // 詳細取得
- UpdateTodoService         // 更新
- ToggleTodoStatusService   // 完了/未完了切替
- DeleteTodoService         // 削除
```

### アクションメソッド

#### 1. index(Request $request): View

**目的**: Todo一覧を表示

**処理フロー**:
1. クエリパラメータ`status`を取得（done/pending/null）
2. statusに応じて適切なサービスメソッドを呼び出し
   - `done`: 完了タスクのみ
   - `pending`: 未完了タスクのみ
   - `null`: 全タスク
3. `todos.index`ビューを返す

**使用サービス**: `ListTodosService`

**URL例**:
- `/todos` - 全タスク
- `/todos?status=done` - 完了タスクのみ
- `/todos?status=pending` - 未完了タスクのみ

---

#### 2. create(): View

**目的**: 新規作成フォームを表示

**処理フロー**:
1. `todos.create`ビューを返す

**使用サービス**: なし

---

#### 3. store(Request $request): RedirectResponse

**目的**: 新しいTodoを保存

**処理フロー**:
1. バリデーション（title: 必須、最大255文字）
2. `CreateTodoService`でTodoを作成
3. 一覧ページにリダイレクト（成功メッセージ付き）

**使用サービス**: `CreateTodoService`

**バリデーションルール**:
```php
'title' => 'required|string|max:255'
```

**エラーハンドリング**:
- `InvalidArgumentException`: バリデーションエラーとして処理

---

#### 4. show(int $id): View

**目的**: Todo詳細を表示

**処理フロー**:
1. `GetTodoDetailService`でTodoを取得
2. Todoが存在しない場合は404エラー
3. `todos.show`ビューを返す

**使用サービス**: `GetTodoDetailService`

---

#### 5. edit(int $id): View

**目的**: 編集フォームを表示

**処理フロー**:
1. `GetTodoDetailService`でTodoを取得
2. Todoが存在しない場合は404エラー
3. `todos.edit`ビューを返す

**使用サービス**: `GetTodoDetailService`

---

#### 6. update(Request $request, int $id): RedirectResponse

**目的**: Todoを更新

**処理フロー**:
1. バリデーション（title: 必須、最大255文字）
2. `UpdateTodoService`でTodoを更新
3. 詳細ページにリダイレクト（成功メッセージ付き）

**使用サービス**: `UpdateTodoService`

**バリデーションルール**:
```php
'title' => 'required|string|max:255'
```

**エラーハンドリング**:
- `RuntimeException`: Todoが見つからない場合、404エラー
- `InvalidArgumentException`: バリデーションエラーとして処理

---

#### 7. destroy(int $id): RedirectResponse

**目的**: Todoを削除

**処理フロー**:
1. `DeleteTodoService`でTodoを削除
2. 一覧ページにリダイレクト（成功メッセージ付き）

**使用サービス**: `DeleteTodoService`

---

#### 8. toggle(int $id): RedirectResponse

**目的**: Todoの完了/未完了状態を切り替え

**処理フロー**:
1. `ToggleTodoStatusService`で状態を切り替え
2. 一覧ページにリダイレクト（成功メッセージ付き）

**使用サービス**: `ToggleTodoStatusService`

**エラーハンドリング**:
- `RuntimeException`: Todoが見つからない場合、404エラー

---

## フラッシュメッセージ

各アクションで成功時に以下のメッセージをセッションに保存：

| アクション | メッセージ |
|-----------|-----------|
| store | タスクを作成しました |
| update | タスクを更新しました |
| destroy | タスクを削除しました |
| toggle | タスクの状態を変更しました |

ビューで以下のように表示可能：

```blade
@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif
```

## バリデーションエラーメッセージ

カスタムエラーメッセージを定義：

```php
'title.required' => 'タイトルは必須です'
'title.max' => 'タイトルは255文字以内で入力してください'
```

## エラーハンドリング

### 404エラー

- Todoが見つからない場合、`abort(404)`でHTTP 404エラーを返す
- カスタムメッセージ付き

### バリデーションエラー

- 入力エラーは`back()->withInput()->withErrors()`で前のページに戻す
- 入力値を保持してエラーメッセージを表示

## ルート確認コマンド

```bash
# 全ルート一覧
php artisan route:list

# Todoルートのみ表示
php artisan route:list --path=todos

# 特定ルートの詳細
php artisan route:list --name=todos.index
```

## 次のステップ

1. ビュー（Bladeテンプレート）の実装
   - `resources/views/todos/index.blade.php`
   - `resources/views/todos/create.blade.php`
   - `resources/views/todos/show.blade.php`
   - `resources/views/todos/edit.blade.php`

2. レイアウトファイルの作成
   - `resources/views/layouts/app.blade.php`

3. 機能テストの実装
   - `tests/Feature/TodoControllerTest.php`

