# BEM CSS設計ドキュメント

## BEMとは

BEM（Block Element Modifier）は、CSSのクラス名を体系的に命名するための方法論です。

### 命名規則

```
.block               /* ブロック */
.block__element      /* エレメント（ブロックの一部） */
.block--modifier     /* モディファイア（ブロックのバリエーション） */
.block__element--modifier  /* エレメントのモディファイア */
```

## プロジェクトのBEM構造

### 1. Header（ヘッダー）

```css
.header                    /* ブロック: ヘッダー全体 */
.header__container         /* エレメント: コンテナ */
.header__logo              /* エレメント: ロゴ */
.header__logo-link         /* エレメント: ロゴのリンク */
.header__nav               /* エレメント: ナビゲーション */
.header__nav-link          /* エレメント: ナビゲーションリンク */
.header__nav-link--active  /* モディファイア: アクティブ状態 */
```

**使用例:**
```html
<header class="header">
    <div class="header__container">
        <h1 class="header__logo">
            <a href="/" class="header__logo-link">Logo</a>
        </h1>
        <nav class="header__nav">
            <a href="/todos" class="header__nav-link header__nav-link--active">一覧</a>
        </nav>
    </div>
</header>
```

---

### 2. Main（メインコンテンツ）

```css
.main                /* ブロック: メインエリア */
.main__container     /* エレメント: コンテナ */
```

---

### 3. Footer（フッター）

```css
.footer              /* ブロック: フッター全体 */
.footer__container   /* エレメント: コンテナ */
.footer__text        /* エレメント: テキスト */
```

---

### 4. Alert（アラート）

```css
.alert                  /* ブロック: アラート */
.alert--success         /* モディファイア: 成功 */
.alert--error           /* モディファイア: エラー */
.alert__icon            /* エレメント: アイコン */
.alert__message         /* エレメント: メッセージ */
.alert__list            /* エレメント: リスト */
.alert__list-item       /* エレメント: リストアイテム */
```

**使用例:**
```html
<!-- 成功メッセージ -->
<div class="alert alert--success">
    <span class="alert__icon">✓</span>
    <span class="alert__message">成功しました</span>
</div>

<!-- エラーメッセージ -->
<div class="alert alert--error">
    <span class="alert__icon">✕</span>
    <span class="alert__message">エラーが発生しました</span>
</div>
```

---

### 5. Page Header（ページヘッダー）

```css
.page-header                /* ブロック: ページヘッダー */
.page-header__title         /* エレメント: タイトル */
.page-header__description   /* エレメント: 説明文 */
```

---

### 6. Card（カード）

```css
.card               /* ブロック: カード */
.card__header       /* エレメント: ヘッダー */
.card__title        /* エレメント: タイトル */
.card__body         /* エレメント: ボディ */
.card__footer       /* エレメント: フッター */
```

**使用例:**
```html
<div class="card">
    <div class="card__header">
        <h2 class="card__title">カードタイトル</h2>
    </div>
    <div class="card__body">
        カードの内容
    </div>
    <div class="card__footer">
        フッター
    </div>
</div>
```

---

### 7. Button（ボタン）

```css
.btn                  /* ブロック: ボタン */
.btn--primary         /* モディファイア: プライマリー */
.btn--secondary       /* モディファイア: セカンダリー */
.btn--success         /* モディファイア: 成功 */
.btn--danger          /* モディファイア: 危険 */
.btn--outline         /* モディファイア: アウトライン */
.btn--small           /* モディファイア: 小サイズ */
.btn--large           /* モディファイア: 大サイズ */
.btn--block           /* モディファイア: ブロック幅 */
```

**使用例:**
```html
<button class="btn btn--primary">保存</button>
<button class="btn btn--danger btn--small">削除</button>
<a href="/create" class="btn btn--secondary btn--large">新規作成</a>
```

---

### 8. Button Group（ボタングループ）

```css
.btn-group           /* ブロック: ボタングループ */
.btn-group--right    /* モディファイア: 右寄せ */
.btn-group--center   /* モディファイア: 中央寄せ */
```

**使用例:**
```html
<div class="btn-group">
    <button class="btn btn--primary">保存</button>
    <button class="btn btn--secondary">キャンセル</button>
</div>
```

---

### 9. Form（フォーム）

```css
.form-group               /* ブロック: フォームグループ */
.form-label               /* ブロック: ラベル */
.form-label--required     /* モディファイア: 必須 */
.form-input               /* ブロック: インプット */
.form-input--error        /* モディファイア: エラー状態 */
.form-error               /* ブロック: エラーメッセージ */
.form-help                /* ブロック: ヘルプテキスト */
```

**使用例:**
```html
<div class="form-group">
    <label class="form-label form-label--required">タイトル</label>
    <input type="text" class="form-input" placeholder="タイトルを入力">
    <p class="form-help">255文字以内で入力してください</p>
</div>

<!-- エラー時 -->
<div class="form-group">
    <label class="form-label form-label--required">タイトル</label>
    <input type="text" class="form-input form-input--error" value="">
    <p class="form-error">タイトルは必須です</p>
</div>
```

---

### 10. Filter Bar（フィルターバー）

```css
.filter-bar                 /* ブロック: フィルターバー */
.filter-bar__label          /* エレメント: ラベル */
.filter-bar__buttons        /* エレメント: ボタングループ */
.filter-bar__button         /* エレメント: ボタン */
.filter-bar__button--active /* モディファイア: アクティブ */
```

**使用例:**
```html
<div class="filter-bar">
    <span class="filter-bar__label">絞り込み:</span>
    <div class="filter-bar__buttons">
        <button class="filter-bar__button filter-bar__button--active">全て</button>
        <button class="filter-bar__button">完了</button>
        <button class="filter-bar__button">未完了</button>
    </div>
</div>
```

---

### 11. Empty State（空状態）

```css
.empty-state           /* ブロック: 空状態 */
.empty-state__icon     /* エレメント: アイコン */
.empty-state__title    /* エレメント: タイトル */
.empty-state__message  /* エレメント: メッセージ */
```

**使用例:**
```html
<div class="empty-state">
    <div class="empty-state__icon">📝</div>
    <h3 class="empty-state__title">タスクがありません</h3>
    <p class="empty-state__message">新しいタスクを作成してください</p>
</div>
```

---

## カラーパレット

```css
/* プライマリーカラー */
--primary: #667eea;
--primary-dark: #764ba2;

/* セカンダリーカラー */
--success: #10b981;
--danger: #ef4444;
--warning: #f59e0b;

/* グレースケール */
--gray-50: #f9fafb;
--gray-100: #f3f4f6;
--gray-200: #e5e7eb;
--gray-300: #d1d5db;
--gray-400: #9ca3af;
--gray-500: #6b7280;
--gray-600: #4b5563;
--gray-700: #374151;
--gray-800: #1f2937;
--gray-900: #111827;

/* 背景色 */
--bg-primary: #f5f7fa;
--bg-white: #ffffff;
```

## タイポグラフィ

```css
/* フォントファミリー */
font-family: 'Noto Sans JP', -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', sans-serif;

/* フォントサイズ */
--text-xs: 0.75rem;    /* 12px */
--text-sm: 0.875rem;   /* 14px */
--text-base: 1rem;     /* 16px */
--text-lg: 1.125rem;   /* 18px */
--text-xl: 1.25rem;    /* 20px */
--text-2xl: 1.5rem;    /* 24px */
--text-3xl: 2rem;      /* 32px */
```

## スペーシング

```css
/* マージン・パディング */
--spacing-xs: 0.25rem;   /* 4px */
--spacing-sm: 0.5rem;    /* 8px */
--spacing-md: 1rem;      /* 16px */
--spacing-lg: 1.5rem;    /* 24px */
--spacing-xl: 2rem;      /* 32px */
--spacing-2xl: 3rem;     /* 48px */
```

## レスポンシブブレークポイント

```css
/* スマートフォン */
@media (max-width: 480px) { ... }

/* タブレット */
@media (max-width: 768px) { ... }

/* デスクトップ */
@media (min-width: 769px) { ... }
```

## BEM命名のベストプラクティス

### ✅ DO（推奨）

```css
.todo-list { }
.todo-list__item { }
.todo-list__item--completed { }
```

### ❌ DON'T（非推奨）

```css
/* 深いネスト */
.todo-list__item__title__text { }  /* 深すぎる */

/* キャメルケース */
.todoList { }  /* BEMではハイフン区切り */

/* 親子関係を無視 */
.todo-list { }
.item { }  /* ブロック名がない */
```

## CSSファイルの構成

```
resources/css/
├── app.css                  # 共通スタイル（BEM方式）
│   ├── リセット & ベース
│   ├── App（ルート）
│   ├── Header
│   ├── Main
│   ├── Footer
│   ├── Alert
│   ├── Page Header
│   ├── Card
│   ├── Button
│   ├── Button Group
│   ├── Form
│   ├── Filter Bar
│   ├── Empty State
│   └── レスポンシブデザイン
│
└── todos/                   # Todosリソース専用スタイル
    ├── common.css           # Todos共通スタイル
    │   └── Additional Button Variants
    │
    ├── index.css            # 一覧ページ専用
    │   ├── Todo List
    │   ├── Todo Item
    │   └── レスポンシブデザイン
    │
    ├── show.css             # 詳細ページ専用
    │   ├── Badge
    │   ├── Detail List
    │   └── レスポンシブデザイン
    │
    └── edit.css             # 編集ページ専用
        └── Info Box
```

### ファイル分割の方針

#### レベル1: アプリケーション全体
- **app.css**: 全ページで使用する共通コンポーネント（ヘッダー、フッター、ボタン、フォームなど）

#### レベル2: リソース単位
- **todos/**: Todosリソース専用のディレクトリ

#### レベル3: ページ単位
- **todos/common.css**: Todosの全ページで共通のスタイル
- **todos/index.css**: 一覧ページ専用のスタイル
- **todos/show.css**: 詳細ページ専用のスタイル
- **todos/edit.css**: 編集ページ専用のスタイル
- **todos/create.blade.php**: common.cssのみ使用（専用スタイルなし）

### CSS読み込みの仕組み

各ビューファイルで`@push('styles')`を使用して、必要なCSSのみを読み込みます：

```blade
@push('styles')
<style>
    {!! file_get_contents(resource_path('css/todos/common.css')) !!}
    {!! file_get_contents(resource_path('css/todos/index.css')) !!}
</style>
@endpush
```

### メリット

✅ **パフォーマンス向上**: 各ページで必要なCSSのみを読み込むため、ファイルサイズが削減  
✅ **保守性向上**: ページごとにスタイルが分離されているため、変更の影響範囲が明確  
✅ **スケーラビリティ**: 新しいページを追加する際も同じパターンで対応可能  
✅ **関心の分離**: リソース単位、ページ単位で明確に分離されている

## 今後の拡張

新しいコンポーネントを追加する場合：

1. **ブロック名を決定**: 例 `.todo-item`
2. **エレメントを定義**: 例 `.todo-item__title`, `.todo-item__status`
3. **モディファイアを追加**: 例 `.todo-item--completed`, `.todo-item--urgent`
4. **app.cssに追加**: セクションを追加して記述

### 例: Todo Item コンポーネント

```css
/* ============================================
   Todo Item
   ============================================ */
.todo-item { }
.todo-item__checkbox { }
.todo-item__title { }
.todo-item__date { }
.todo-item__actions { }
.todo-item--completed { }
.todo-item--pending { }
```

