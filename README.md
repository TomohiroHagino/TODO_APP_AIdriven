# 📝 Todo App - DDD User Aggregate Architecture

LaravelとDDD（ドメイン駆動設計）で実装した認証付きTodoアプリケーションです。

**User Aggregate Root**を中心とした本格的なDDDアーキテクチャを採用し、ドメインモデルとインフラストラクチャを明確に分離しています。

## ✨ 主な特徴

- ✅ **DDD (Domain-Driven Design)**: User Aggregateを中心とした設計
- ✅ **レイヤードアーキテクチャ**: Domain、Application、Infrastructure、Presentationの4層構造
- ✅ **認証機能**: Laravel Breeze統合
- ✅ **ValueObject**: ドメインロジックのカプセル化
- ✅ **Repository Pattern**: データアクセスの抽象化
- ✅ **モダンUI**: Tailwind CSS + Breeze

## 🏗 アーキテクチャ

### User Aggregate

このアプリケーションは**User Aggregate**を中心に設計されています。

```
User (Aggregate Root)
├── UserId (ValueObject)
├── UserName (ValueObject)
├── Email (ValueObject)
└── Todos (子エンティティのコレクション)
    └── Todo (子エンティティ)
        ├── TodoId (ValueObject)
        ├── TaskTitle (ValueObject)
        ├── TaskStatus (ValueObject)
        └── DateTimeValue (ValueObject)
```

**重要な原則**:
- TodoはUserに所有される（Userなしでは存在しない）
- TodoへのすべてのCRUD操作はUser経由で行う
- User削除時にTodoも自動削除される（Cascade）

### ディレクトリ構造

```
app/
├── Domain/                            # ドメイン層
│   ├── Shared/                        # 共通部品
│   │   ├── ValueObject/
│   │   │   ├── Id.php                 # ID基底クラス
│   │   │   └── DateTimeValue.php     # 日時値オブジェクト
│   │   └── Exception/
│   │       └── DomainException.php    # ドメイン例外基底
│   │
│   └── UserAggregate/                 # User集約
│       ├── Entity/
│       │   ├── User.php               # Aggregate Root
│       │   └── Todo.php               # 子エンティティ
│       ├── ValueObject/
│       │   ├── UserId.php
│       │   ├── UserName.php
│       │   ├── Email.php
│       │   ├── TodoId.php
│       │   ├── TaskTitle.php
│       │   └── TaskStatus.php
│       ├── Repository/
│       │   └── UserRepositoryInterface.php
│       ├── Event/                     # ドメインイベント
│       │   ├── UserCreated.php
│       │   ├── TodoAdded.php
│       │   └── TodoUpdated.php
│       └── Service/
│           └── UserDomainService.php  # ドメインサービス
│
├── Application/                        # アプリケーション層
│   └── UserAggregate/
│       └── Service/                    # ユースケース実装
│           ├── AddTodoToUserService.php
│           ├── UpdateTodoOfUserService.php
│           ├── ToggleTodoStatusService.php
│           ├── DeleteTodoOfUserService.php
│           └── GetUserTodosService.php
│
├── Infrastructure/                     # インフラ層
│   └── UserAggregate/
│       └── Repository/
│           └── UserRepository.php      # Eloquent実装
│
├── Http/                               # プレゼンテーション層
│   ├── Controllers/
│   │   └── TodoController.php
│   └── Requests/
│       ├── StoreTodoRequest.php
│       └── UpdateTodoRequest.php
│
└── Models/                             # Eloquent ORM
    ├── User.php                        # UserModel (認証用)
    └── TodoModel.php
```

### レイヤー間の依存関係

```
Presentation (HTTP, View)
    ↓ depends on
Application (Use Cases)
    ↓ depends on
Domain (Business Logic) ← CORE
    ↑ implements
Infrastructure (DB, External)
```

**ルール**:
- Domain層は他の層に依存しない
- Application層はDomain層のみに依存
- Infrastructure層はDomain層のインターフェースを実装
- Presentation層はApplication層を呼び出す

## 🚀 セットアップ

### 必要要件

- PHP 8.2以上
- Composer
- SQLite（またはMySQL/PostgreSQL）
- Node.js & npm

### インストール手順

```bash
# 依存パッケージをインストール
composer install
npm install

# 環境設定ファイルをコピー
cp .env.example .env

# アプリケーションキーを生成
php artisan key:generate

# データベースマイグレーション
php artisan migrate

# アセットをビルド
npm run dev
```

## 🎮 アプリケーションの起動

### 開発サーバー起動

```bash
# Laravelサーバー起動
php artisan serve

# 別ターミナルでVite起動
npm run dev
```

http://localhost:8000 にアクセス

### 初回利用

1. **ユーザー登録**: `/register` から新規ユーザー作成
2. **ログイン**: `/login` からログイン
3. **Todo作成**: 認証後、自動的にTodo一覧画面へ

## 📖 ユースケース

### 1. ユーザーにTodoを追加する
**Service**: `AddTodoToUserService`

**フロー**:
1. 認証ユーザーを取得
2. 新しいTodoIDを採番
3. UserにTodoを追加
4. Userを保存（Todoも一緒に保存）

### 2. ユーザーのTodoを更新する
**Service**: `UpdateTodoOfUserService`

**フロー**:
1. Userを取得
2. UserからTodoを検索
3. Todoのタイトルを変更
4. Userを保存

### 3. Todoステータスを切り替える
**Service**: `ToggleTodoStatusService`

**フロー**:
1. Userを取得
2. UserからTodoを検索
3. Todoのステータスを切り替え
4. Userを保存

### 4. ユーザーのTodoを削除する
**Service**: `DeleteTodoOfUserService`

**フロー**:
1. Userを取得
2. UserからTodoを削除
3. Userを保存

### 5. ユーザーのTodo一覧を取得する
**Service**: `GetUserTodosService`

**フロー**:
1. Userを取得
2. Userの全Todosを取得
3. フィルター適用（必要に応じて）

## 🧪 テスト

```bash
# 全テスト実行
php artisan test

# カバレッジ付きテスト実行
XDEBUG_MODE=coverage php artisan test --coverage

# 特定のテストを実行
php artisan test tests/Feature/TodoControllerTest.php
```

## 🛠 技術スタック

- **Framework**: Laravel 12
- **Authentication**: Laravel Breeze
- **Frontend**: Blade + Tailwind CSS
- **Database**: SQLite (デフォルト)
- **Testing**: PHPUnit + Pest
- **Architecture**: DDD + Layered Architecture

## 🔑 主要な設計パターン

### 1. Aggregate Pattern
User AggregateがTodoを所有し、整合性を保証

### 2. Repository Pattern
データアクセスを抽象化し、Domain層をインフラから分離

### 3. Value Object
不変の値オブジェクトでドメインルールをカプセル化

### 4. Application Service
ユースケースを1つのトランザクション単位で実装

### 5. Dependency Injection
Laravel Service Containerによる疎結合

## 📝 主要なコマンド

```bash
# マイグレーション
php artisan migrate
php artisan migrate:fresh  # 全テーブル削除して再作成

# テスト
php artisan test
php artisan test --filter TodoControllerTest

# キャッシュクリア
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# コード生成
php artisan make:migration CreateXxxTable
php artisan make:model XxxModel
php artisan make:controller XxxController
```

## 🎯 今後の拡張案

- [ ] Todo に期限（deadline）追加
- [ ] TodoをProjectで分類
- [ ] Todoへのタグ付け機能
- [ ] TodoのソートとフィルタリングUI
- [ ] API化（Laravel Sanctum）
- [ ] Event Sourcing導入
- [ ] CQRS パターン適用

## 📚 参考資料

- [Domain-Driven Design by Eric Evans](https://www.domainlanguage.com/ddd/)
- [Laravel Documentation](https://laravel.com/docs)
- [Clean Architecture by Robert C. Martin](https://blog.cleancoder.com/uncle-bob/2012/08/13/the-clean-architecture.html)

## 📄 ライセンス

MIT License

---

Made with ❤️ using Laravel & DDD
