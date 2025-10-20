# データモデリング設計書

## 概要

このドキュメントは、Todoアプリケーションのデータモデリングについて説明します。

## ER図

```
┌─────────────────────┐
│       todos         │
├─────────────────────┤
│ id          PK      │
│ title               │
│ is_done             │
│ created_at          │
└─────────────────────┘
```

## テーブル定義

### todosテーブル

Todoタスクの情報を管理するテーブル

| カラム名 | データ型 | NULL | デフォルト値 | 説明 |
|---------|---------|------|------------|------|
| id | BIGINT UNSIGNED | NO | AUTO_INCREMENT | 主キー |
| title | VARCHAR(255) | NO | - | タスクのタイトル |
| is_done | BOOLEAN | NO | false | 完了フラグ（true: 完了、false: 未完了） |
| created_at | TIMESTAMP | NO | CURRENT_TIMESTAMP | 作成日時 |

#### インデックス

- PRIMARY KEY: `id`
- INDEX: `is_done` (完了/未完了での検索を高速化)
- INDEX: `created_at` (作成日時でのソートを高速化)

## ドメインモデルとの対応

### Todoエンティティ

```php
class Todo
{
    private int $id;                    // → todos.id
    private TaskTitle $title;           // → todos.title
    private bool $isDone;               // → todos.is_done
    private \DateTimeImmutable $createdAt; // → todos.created_at
}
```

### Value Object

#### TaskTitle

- バリデーション: 1文字以上255文字以下
- トリミング処理を実行

## レイヤー構成

### Domain層
- `Entity\Todo`: Todoエンティティ（ビジネスロジック）
- `ValueObject\TaskTitle`: タスクタイトルの値オブジェクト
- `Repository\TodoRepositoryInterface`: リポジトリインターフェース

### Infrastructure層
- `Repository\TodoRepository`: Eloquentを使用したリポジトリ実装
- `Models\TodoModel`: EloquentモデルでDBとマッピング

### Application層
- `Service\CreateTodoService`: Todo作成
- `Service\ListTodosService`: Todo一覧取得
- `Service\GetTodoDetailService`: Todo詳細取得
- `Service\UpdateTodoService`: Todo更新
- `Service\ToggleTodoStatusService`: Todo完了/未完了切替
- `Service\DeleteTodoService`: Todo削除

## マイグレーション

マイグレーションファイル: `database/migrations/2025_10_20_000001_create_todos_table.php`

実行コマンド:
```bash
php artisan migrate
```

ロールバックコマンド:
```bash
php artisan migrate:rollback
```

## 設計上の考慮事項

### 1. シンプルなスキーマ
- Todoアプリケーションの最小限の要件を満たすシンプルな設計
- 拡張性を考慮しつつ、過度な複雑化を避ける

### 2. パフォーマンス
- `is_done`と`created_at`にインデックスを設定
- 一覧表示時の検索・ソートを高速化

### 3. DDD（ドメイン駆動設計）
- ドメイン層とインフラ層を分離
- リポジトリパターンでデータアクセスを抽象化
- エンティティとEloquentモデルを分離し、ドメインロジックの純粋性を保つ

### 4. データ整合性
- `title`は必須項目
- `is_done`はデフォルトでfalse（未完了）
- `created_at`は作成時に自動設定

## 今後の拡張案

必要に応じて以下の拡張が可能です：

1. **ユーザー管理との連携**
   - `user_id`カラムの追加
   - usersテーブルとのリレーション

2. **タスクの優先度**
   - `priority`カラムの追加（高・中・低）

3. **期限管理**
   - `due_date`カラムの追加

4. **カテゴリ・タグ**
   - `categories`テーブルの追加
   - 多対多のリレーション

5. **更新日時の追加**
   - `updated_at`カラムの追加

6. **ソフトデリート**
   - `deleted_at`カラムの追加
   - 論理削除の実装

