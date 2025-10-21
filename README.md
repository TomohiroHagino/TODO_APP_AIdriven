# 📝 Todo App

LaravelとDDD（ドメイン駆動設計）で実装したシンプルなTodoアプリケーションです。

## 📋 目次

- [機能](#機能)
- [ユースケース](#ユースケース)
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

## 📖 ユースケース

このアプリケーションでは、6つのユースケースを実装しています。各ユースケースは`app/Application/Todo/Service/`ディレクトリ内のサービスクラスとして実装されています。

### 1. タスクを作成する（CreateTodoService）

**アクター**: ユーザー  
**目的**: 新しいタスクを登録する

**メインフロー**:
1. ユーザーが「新規作成」ボタンをクリック
2. システムが作成フォームを表示
3. ユーザーがタスク名を入力（1〜255文字）
4. ユーザーが「作成」ボタンをクリック
5. システムがタスクをデータベースに保存
6. システムが一覧画面にリダイレクト
7. システムが成功メッセージを表示

**実装**: [`CreateTodoService.php`](app/Application/Todo/Service/CreateTodoService.php)

---

### 2. タスク一覧を表示する（ListTodosService）

**アクター**: ユーザー  
**目的**: 登録されているタスクの一覧を確認する

**メインフロー**:
1. ユーザーが一覧ページにアクセス
2. システムが全タスクをデータベースから取得
3. システムがタスクを作成日時の降順で並び替え
4. システムが一覧画面を表示

**代替フロー**:
- ユーザーが「完了」フィルターを選択 → 完了タスクのみ表示
- ユーザーが「未完了」フィルターを選択 → 未完了タスクのみ表示

**実装**: [`ListTodosService.php`](app/Application/Todo/Service/ListTodosService.php)

---

### 3. タスクの詳細を表示する（GetTodoDetailService）

**アクター**: ユーザー  
**目的**: 特定のタスクの詳細情報を確認する

**メインフロー**:
1. ユーザーが一覧画面でタスクを選択
2. ユーザーが「詳細」ボタンまたはタスク名をクリック
3. システムが指定されたIDのタスクを取得
4. システムがタスクの詳細情報を表示
   - タスク名
   - ステータス（完了/未完了）
   - 作成日時
   - タスクID

**実装**: [`GetTodoDetailService.php`](app/Application/Todo/Service/GetTodoDetailService.php)

---

### 4. タスクを編集する（UpdateTodoService）

**アクター**: ユーザー  
**目的**: タスクの内容を変更する

**メインフロー**:
1. ユーザーが詳細画面または一覧画面で「編集」ボタンをクリック
2. システムが編集フォームを表示（既存のタイトルを入力済み）
3. ユーザーが新しいタスク名を入力
4. ユーザーが「更新」ボタンをクリック
5. システムがタスク名をバリデーション
6. システムがタスクのタイトルを更新
7. システムが詳細画面にリダイレクト
8. システムが成功メッセージを表示

**実装**: [`UpdateTodoService.php`](app/Application/Todo/Service/UpdateTodoService.php)

---

### 5. タスクの完了状態を切り替える（ToggleTodoStatusService）

**アクター**: ユーザー  
**目的**: タスクを完了/未完了の状態に切り替える

**メインフロー**:
1. ユーザーが一覧画面でタスクのチェックボックスをクリック
2. システムがタスクの完了状態を反転
   - 未完了 → 完了
   - 完了 → 未完了
3. システムが変更をデータベースに保存
4. システムが一覧画面を再表示
5. システムがタスクの表示を更新
   - 完了タスク: 打ち消し線、グレー背景、チェックマーク
   - 未完了タスク: 通常表示

**実装**: [`ToggleTodoStatusService.php`](app/Application/Todo/Service/ToggleTodoStatusService.php)

---

### 6. タスクを削除する（DeleteTodoService）

**アクター**: ユーザー  
**目的**: 不要なタスクをシステムから削除する

**メインフロー**:
1. ユーザーが削除対象のタスクを選択
2. ユーザーが削除ボタンをクリック
3. ユーザーが削除の確認ダイアログで「OK」を選択
4. システムがタスクをデータベースから削除
5. システムが一覧画面にリダイレクト
6. システムが成功メッセージを表示

**実装**: [`DeleteTodoService.php`](app/Application/Todo/Service/DeleteTodoService.php)

---

### ユースケースの実装パターン

このアプリケーションでは、**1クラス = 1ユースケース**の原則で実装されています。

```php
// 例: CreateTodoService.php
class CreateTodoService
{
    public function handle(string $title): void
    {
        // ユースケースのフローを実装
        $id = $this->repository->nextId();
        $todo = new Todo($id, new TaskTitle($title), false, new \DateTimeImmutable());
        $this->repository->save($todo);
    }
}
```

各サービスクラスのソースコードには、詳細なユースケース仕様がコメントとして記載されています。

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

# カバレッジ付きで実行
XDEBUG_MODE=coverage php artisan test --coverage

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
