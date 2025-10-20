# 📝 Todo App

LaravelとDDD（ドメイン駆動設計）で実装したシンプルなTodoアプリケーションです。

## 📋 目次

- [機能](#機能)
- [技術スタック](#技術スタック)
- [アーキテクチャ](#アーキテクチャ)
- [セットアップ](#セットアップ)
- [アプリケーションの起動](#アプリケーションの起動)
- [開発](#開発)
- [ドキュメント](#ドキュメント)

## ✨ 機能

- ✅ タスクの作成
- ✅ タスク一覧の表示
- ✅ タスクの詳細表示
- ✅ タスクの編集
- ✅ タスクの削除
- ✅ タスクの完了/未完了切替
- ✅ ステータスでの絞り込み（全て/完了/未完了）

## 🛠 技術スタック

### バックエンド
- **PHP**: 8.4.8
- **Laravel**: 12.34.0
- **SQLite**: 3.43.2

### フロントエンド
- **HTML/CSS**: BEM方式
- **Blade**: テンプレートエンジン

### アーキテクチャ
- **DDD**: ドメイン駆動設計
- **Repository Pattern**: データアクセスの抽象化
- **Service Layer**: ユースケースの実装

## 🏗 アーキテクチャ

```
app/
├── Application/          # アプリケーション層（ユースケース）
│   └── Todo/Service/
├── Domain/               # ドメイン層（ビジネスロジック）
│   └── Todo/
│       ├── Entity/       # エンティティ
│       ├── ValueObject/  # 値オブジェクト
│       └── Repository/   # リポジトリインターフェース
├── Infrastructure/       # インフラ層（データ永続化）
│   └── Todo/Repository/
├── Http/                 # プレゼンテーション層
│   └── Controllers/
└── Models/               # Eloquent Model
```

### レイヤー構成

```
Presentation Layer (Controller)
        ↓
Application Layer (Service/UseCase)
        ↓
Domain Layer (Entity/ValueObject)
        ↓
Infrastructure Layer (Repository)
        ↓
Database (SQLite)
```

## 🚀 セットアップ

### 必要要件

- PHP 8.2以上
- Composer
- SQLite

### インストール

```bash
# リポジトリのクローン
git clone <repository-url>
cd testapp

# 依存関係のインストール
composer install

# データベースのマイグレーション
php artisan migrate
```

## 🎮 アプリケーションの起動

### 開発サーバーの起動

```bash
# Laravelの開発サーバーを起動
php artisan serve
```

ブラウザで以下のURLにアクセス:
```
http://localhost:8000
```

### ポート番号を指定する場合

```bash
# 別のポートで起動する場合
php artisan serve --port=8080
```

### ホストを指定する場合

```bash
# ネットワーク経由でアクセス可能にする場合
php artisan serve --host=0.0.0.0 --port=8000
```

### バックグラウンドで起動

```bash
# バックグラウンドで起動
php artisan serve &

# プロセスを確認
ps aux | grep artisan

# 停止する場合
pkill -f "artisan serve"
```

### トラブルシューティング

#### ポートが使用中の場合

```bash
# ポート8000が使用中の場合、別のポートを使用
php artisan serve --port=8001
```

#### "Operation not permitted"エラーの場合

```bash
# すでにサーバーが起動していないか確認
lsof -i :8000

# プロセスを終了
kill -9 <PID>
```

## 🔧 開発

### データベースのリセット

```bash
# データベースを再作成
php artisan migrate:fresh
```

### ルート一覧の確認

```bash
# 全ルート表示
php artisan route:list

# Todoルートのみ表示
php artisan route:list --path=todos
```

### データベースの状態確認

```bash
# データベース情報表示
php artisan db:show

# テーブル構造表示
php artisan db:table todos
```

### テストの実行

```bash
# 全テスト実行
php artisan test

# 特定のテストのみ実行
php artisan test --filter=CreateTodoServiceTest
```

### コードスタイル

```bash
# Laravel Pintでコード整形
./vendor/bin/pint
```

## 📚 ドキュメント

プロジェクトのドキュメントは`docs/`ディレクトリに格納されています：

- **[データモデリング](docs/data-modeling.md)** - データベース設計
- **[コントローラー&ルーティング](docs/controller-routing.md)** - HTTPレイヤー
- **[BEM CSS設計](docs/bem-css-design.md)** - CSSアーキテクチャ

## 📂 ディレクトリ構造

```
testapp/
├── app/
│   ├── Application/          # アプリケーション層
│   ├── Domain/               # ドメイン層
│   ├── Infrastructure/       # インフラ層
│   ├── Http/                 # HTTPレイヤー
│   └── Models/               # Eloquentモデル
├── database/
│   ├── migrations/           # マイグレーション
│   └── database.sqlite       # SQLiteデータベース
├── resources/
│   ├── css/                  # CSSファイル
│   └── views/                # Bladeテンプレート
│       ├── layouts/
│       └── todos/
├── routes/
│   └── web.php               # Webルート定義
├── tests/                    # テスト
│   ├── Unit/
│   └── Feature/
└── docs/                     # ドキュメント
```

## 🎨 主要な画面

### 一覧画面 (`/todos`)
- タスク一覧の表示
- ステータスでの絞り込み
- 完了/未完了の切り替え

### 作成画面 (`/todos/create`)
- 新規タスクの作成

### 詳細画面 (`/todos/{id}`)
- タスクの詳細情報
- ステータス変更
- 編集・削除

### 編集画面 (`/todos/{id}/edit`)
- タスク名の編集

## 🧪 テスト

```bash
# 全テスト実行
php artisan test

# カバレッジ付きで実行
php artisan test --coverage

# 特定のディレクトリのみ
php artisan test tests/Unit/Domain
```

## 📝 主要なコマンド

```bash
# データベース
php artisan migrate              # マイグレーション実行
php artisan migrate:fresh        # データベースリセット
php artisan db:show             # データベース情報表示

# ルーティング
php artisan route:list          # ルート一覧
php artisan route:cache         # ルートキャッシュ

# 開発サーバー
php artisan serve               # 開発サーバー起動
php artisan serve --port=8080   # ポート指定

# テスト
php artisan test                # テスト実行
```

## 🤝 貢献

バグ報告や機能リクエストは、Issuesページでお願いします。

## 📄 ライセンス

このプロジェクトは[MIT License](https://opensource.org/licenses/MIT)のもとで公開されています。

---

**開発者**: Tomohiro Hagino  
**最終更新**: 2025年10月20日
