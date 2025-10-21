# 📝 Todo App - DDD User Aggregate Architecture

LaravelとDDD（ドメイン駆動設計）で実装した認証付きTodoアプリケーションです。  
**User Aggregate Root**を中心とした本格的なDDDアーキテクチャを採用し、  
ドメインモデルとインフラストラクチャを明確に分離しています。

---

## ✨ 主な特徴

- ✅ **DDD (Domain-Driven Design)**: User Aggregateを中心とした設計  
- ✅ **レイヤードアーキテクチャ**: Domain、Application、Infrastructure、Presentationの4層構造  
- ✅ **認証機能**: Laravel Breeze統合  
- ✅ **ValueObject**: ドメインロジックのカプセル化  
- ✅ **Repository Pattern**: データアクセスの抽象化  
- ✅ **モダンUI**: BEM CSS (カスタムデザイン) + Laravel Breeze (認証)  

---

## 📚 目次

1. [機能](#-機能)
2. [ユースケース](#-ユースケース)
3. [主な特徴](#-主な特徴)
4. [アーキテクチャ](#-アーキテクチャ)
5. [セットアップ](#-セットアップ)
6. [アプリケーションの起動](#-アプリケーションの起動)
7. [初回利用](#-初回利用)
8. [テスト](#-テスト)
9. [技術スタック](#-技術スタック)
10. [主要な設計パターン](#-主要な設計パターン)
11. [「2つのモデル」問題の解決](#-2つのモデル問題の解決)
12. [主要なコマンド](#-主要なコマンド)
13. [今後の拡張案](#-今後の拡張案)
14. [参考資料](#-参考資料)
15. [ライセンス](#-ライセンス)

---

## ✨ 機能（Features）

- ✅ タスクの作成  
- ✅ タスク一覧の表示  
- ✅ タスクの詳細表示  
- ✅ タスクの編集  
- ✅ タスクの削除  
- ✅ タスクの完了／未完了切替  
- ✅ ステータスでの絞り込み（全て／完了／未完了）  
- ✅ 認証（ログイン／新規登録／ログアウト）  
- ✅ プロフィール編集  

---

## 📖 ユースケース（Use Cases）

| ユースケース名 | 対応Service | 概要 |
|----------------|--------------|------|
| ユーザーにTodoを追加する | `AddTodoToUserService` | 認証ユーザーを取得し、新しいTodoIDを採番してUserに追加後、保存する |
| ユーザーのTodoを更新する | `UpdateTodoOfUserService` | UserからTodoを検索し、タイトルを更新して保存する |
| Todoステータスを切り替える | `ToggleTodoStatusService` | UserからTodoを検索し、完了／未完了を切り替える |
| ユーザーのTodoを削除する | `DeleteTodoOfUserService` | Userから指定Todoを削除して保存する |
| ユーザーのTodo一覧を取得する | `GetUserTodosService` | Userの全Todoを取得し、フィルター処理を行う |

---

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
│       │   ├── UserEntity.php         # Aggregate Root
│       │   └── TodoEntity.php         # 子エンティティ
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
    ├── User.php                        # Eloquent Model (認証用)
    └── Todo.php                        # Eloquent Model
```

### View & CSS 構造

```
resources/
├── views/
│   ├── layouts/
│   │   └── main.blade.php              # カスタムBEMレイアウト
│   │
│   ├── components/
│   │   ├── header.blade.php            # カスタムヘッダーコンポーネント
│   │   └── breeze/                     # Laravel Breeze認証コンポーネント
│   │       ├── app-layout.blade.php
│   │       ├── guest-layout.blade.php
│   │       ├── navigation.blade.php
│   │       └── (その他13個のコンポーネント)
│   │
│   ├── todos/                          # Todoページビュー
│   │   ├── index.blade.php
│   │   ├── create.blade.php
│   │   ├── show.blade.php
│   │   └── edit.blade.php
│   │
│   ├── auth/                           # 認証ページ（Breeze）
│   └── profile/                        # プロフィール
│
└── css/
    ├── app.css                         # エントリーポイント
    ├── common.css                      # 共通スタイル（ヘッダー、ボタン、レイアウト）
    └── todos/                          # ページ固有スタイル
        ├── index.css
        ├── show.css
        └── edit.css
```

**設計原則**:
- `layouts/`: カスタムレイアウト（`main.blade.php`のみ）
- `components/`: 再利用可能なコンポーネント
- `components/breeze/`: Laravel Breeze関連を分離
- `css/common.css`: アプリ全体の共通スタイル
- `css/todos/`: Todosページ固有のスタイル

### レイヤー間の依存関係

```
🧩 Presentation
        ↓
⚙️ Application
        ↓
💡 Domain
        ↑
🗄️ Infrastructure
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
# アセットをビルド（初回またはCSSを変更した場合）
npm run build

# Laravelサーバー起動
php artisan serve
```

**ホットリロード開発の場合**:
```bash
# ターミナル1: Laravelサーバー
php artisan serve

# ターミナル2: Vite（ホットリロード）
npm run dev
```

http://localhost:8000 にアクセス

## 🧑‍💻 初回利用

### 1. アカウント作成

1. ブラウザで `http://localhost:8000/register` にアクセス  
2. 新規登録ページで以下を入力：  
   - Name: 任意のユーザー名（例: 太郎）  
   - Email: 有効なメールアドレス（例: taro@example.com）  
   - Password: 8文字以上  
   - Confirm Password: 同一のパスワード  
3. Registerボタンをクリック  
4. 登録成功後 `/todos` にリダイレクト  

### 2. ログイン

1. `http://localhost:8000/login` にアクセス  
2. 登録済みメールアドレスとパスワードを入力  
3. Log inボタンをクリック  
4. `/todos` ページへ遷移  

---

## 🧪 テスト

（テスト実行コマンド）

---

## 🛠 技術スタック

（Laravel / Breeze / Blade / BEM / SQLite / Pest）

---

## 🔑 主要な設計パターン

（Aggregate / Repository / Value Object / Application Service / DI）

---

## 🤔 「2つのモデル」問題の解決

（EloquentとDomain Entityの分離解説）

---

## 📝 主要なコマンド

（artisanコマンド集）

---

## 🎯 今後の拡張案

- [ ] Todo に期限（deadline）追加  
- [ ] TodoをProjectで分類  
- [ ] Todoへのタグ付け機能  
- [ ] TodoのソートとフィルタリングUI  
- [ ] API化（Laravel Sanctum）  
- [ ] Event Sourcing導入  
- [ ] CQRS パターン適用  

---

## 📚 参考資料

- [Domain-Driven Design by Eric Evans](https://www.domainlanguage.com/ddd/)  
- [Laravel Documentation](https://laravel.com/docs)  
- [Clean Architecture by Robert C. Martin](https://blog.cleancoder.com/uncle-bob/2012/08/13/the-clean-architecture.html)  

---

## 📄 ライセンス

MIT License  

---

Made with ❤️ using Laravel & DDD
