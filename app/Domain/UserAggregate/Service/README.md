# User Aggregate Domain Service

このディレクトリは、**User Aggregate固有のドメインサービス**を配置するためのものです。

## 📝 配置すべきクラス

User Aggregateに関連する、以下の条件を満たすドメインロジック：

- ✅ **User Aggregateに特化**したビジネスルール
- ✅ **Repositoryアクセスが必要**なドメインロジック
- ✅ **Entity内に書くには複雑すぎる**ロジック
- ✅ **複数のUserインスタンス間の操作**（User固有のビジネスルール）
- ❌ どのAggregateでも使える汎用ロジックは `Domain/Shared/Service/` へ

## 💡 配置例

### 例1: メールアドレス変更ポリシー

```php
<?php

namespace App\Domain\UserAggregate\Service;

use App\Domain\UserAggregate\Entity\UserEntity;
use App\Domain\UserAggregate\Repository\UserRepositoryInterface;
use App\Domain\UserAggregate\ValueObject\Email;

/**
 * メールアドレス変更に関するドメインルール
 */
class EmailChangePolicy
{
    public function __construct(
        private UserRepositoryInterface $repository
    ) {}

    /**
     * メールアドレスを変更可能か判定
     * 
     * ビジネスルール:
     * - 既に使用されているメールアドレスは不可
     * - 過去30日以内に変更している場合は不可
     * - 未検証のメールアドレスがある場合は不可
     */
    public function canChangeEmail(UserEntity $user, Email $newEmail): bool
    {
        // 1. 重複チェック
        $existingUser = $this->repository->findByEmail($newEmail);
        if ($existingUser && !$existingUser->getId()->equals($user->getId())) {
            return false;
        }
        
        // 2. 変更頻度チェック
        if ($user->hasChangedEmailWithin30Days()) {
            return false;
        }
        
        // 3. 未検証メールチェック
        if ($user->hasUnverifiedEmail()) {
            return false;
        }
        
        return true;
    }
}
```

### 例2: ユーザー名重複チェック

```php
<?php

namespace App\Domain\UserAggregate\Service;

use App\Domain\UserAggregate\Repository\UserRepositoryInterface;
use App\Domain\UserAggregate\ValueObject\UserName;
use App\Domain\UserAggregate\ValueObject\UserId;

/**
 * ユーザー名の重複チェックサービス
 */
class UserNameDuplicationChecker
{
    public function __construct(
        private UserRepositoryInterface $repository
    ) {}

    /**
     * ユーザー名が使用可能かチェック
     */
    public function isAvailable(UserName $userName, ?UserId $excludeUserId = null): bool
    {
        $existingUser = $this->repository->findByUserName($userName);
        
        if (!$existingUser) {
            return true;
        }
        
        // 自分自身のユーザー名ならOK
        if ($excludeUserId && $existingUser->getId()->equals($excludeUserId)) {
            return true;
        }
        
        return false;
    }
}
```

### 例3: Todo移譲サービス（複数Userインスタンス間の操作）

```php
<?php

namespace App\Domain\UserAggregate\Service;

use App\Domain\Shared\Exception\DomainException;
use App\Domain\UserAggregate\Repository\UserRepositoryInterface;
use App\Domain\UserAggregate\ValueObject\TodoId;
use App\Domain\UserAggregate\ValueObject\UserId;

/**
 * User Aggregate固有のドメインサービス
 * 
 * 複数のUserインスタンス間でTodoを移譲する
 * User Aggregateのビジネスルールなので、User/Service/ に配置
 */
class TodoTransferService
{
    public function __construct(
        private UserRepositoryInterface $repository
    ) {}

    /**
     * TodoをユーザーAからユーザーBへ移譲
     * 
     * ビジネスルール:
     * - 完了したTodoは移譲できない（User Aggregateのルール）
     * - 移譲元が所有するTodoでなければならない
     * - 移譲先ユーザーが存在しなければならない
     */
    public function transfer(
        UserId $fromUserId,
        UserId $toUserId,
        TodoId $todoId
    ): void {
        // 2つのUserインスタンスを取得
        $fromUser = $this->repository->findById($fromUserId);
        $toUser = $this->repository->findById($toUserId);
        
        if (!$fromUser) {
            throw new DomainException("移譲元ユーザーが見つかりません");
        }
        
        if (!$toUser) {
            throw new DomainException("移譲先ユーザーが見つかりません");
        }
        
        // Todoを取得し、ビジネスルールをチェック
        $todo = $fromUser->findTodo($todoId);
        if (!$todo) {
            throw new DomainException("指定されたTodoが見つかりません");
        }
        
        // User Aggregateのビジネスルール: 完了したTodoは移譲できない
        if ($todo->isDone()) {
            throw new DomainException("完了したTodoは移譲できません");
        }
        
        // 移譲実行
        $fromUser->removeTodo($todoId);
        $toUser->addTodo(
            new TodoId($this->repository->nextTodoId()),
            $todo->getTitle()
        );
        
        // 両方のUserインスタンスを保存
        $this->repository->save($fromUser);
        $this->repository->save($toUser);
    }
}
```

**注記**: このサービスは2つの**Userインスタンス**を操作しますが、**User Aggregateに特化した**ビジネスルールを扱うため、`Domain/UserAggregate/Service/` に配置します。

## 🚫 配置すべきでないもの

- ❌ **Application Service**（ユースケース実装） → `Application/UserAggregate/Service/` へ
- ❌ **Entity内で完結するロジック** → `UserEntity.php` のメソッドとして実装
- ❌ **複数Aggregateをまたがるロジック** → `Domain/Shared/Service/` へ
- ❌ **Infrastructure層の実装** → `Infrastructure/UserAggregate/` へ

## 📚 参考

### Domain Service vs Application Service

| 項目 | Domain Service | Application Service |
|------|----------------|---------------------|
| **レイヤー** | Domain層 | Application層 |
| **責務** | ドメインロジック | ユースケース実現 |
| **依存** | Repository Interface | Repository + Domain Service |
| **再利用性** | 高い（複数箇所から利用） | 低い（1ユースケースに特化） |

### いつ作成するか？

以下の状況になったら、Domain Serviceの作成を検討してください：

1. **Repositoryアクセスが必要**なビジネスルールがある
2. **Entity内に書くと責務が大きくなりすぎる**
3. **同じドメインロジックを複数のApplication Serviceで使いたい**
4. **複雑なバリデーションルール**がある（例: 外部データとの照合）

それまでは、このディレクトリは空のままで問題ありません。

