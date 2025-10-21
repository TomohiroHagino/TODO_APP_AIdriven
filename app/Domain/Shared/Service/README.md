# Shared Domain Service

このディレクトリは、**複数のAggregateで共有されるドメインサービス**を配置するためのものです。

## 📝 配置すべきクラス

以下の条件を満たすドメインロジック：

### ✅ 配置すべきもの

1. **異なる種類のAggregateをまたがる**ビジネスルール
   - 例: UserとProjectの協調（UserProjectAssignmentService）
   
2. **複数のAggregateで共有すべき共通ビジネスルール**
   - 例: パスワード強度チェック（複数のAggregate型で使用）
   - 例: ID生成ルール（全Aggregateで共通）

### ❌ 配置すべきでないもの

- 特定のAggregateに特化したものは `Domain/{Aggregate}/Service/` へ
- 純粋な技術的ユーティリティ（ビジネスルール無し）は `Infrastructure/` へ

## 🤔 重要な原則

### 「どこにも属さない」ビジネスロジックについて

DDDでは、**すべての重要なビジネスロジックは何らかのAggregateに属するべき**です。

もし「どのAggregateにも属さない重要なビジネスルール」を発見したら：
1. **新しいAggregate**を作る機会かもしれません
2. または既存のAggregateの責務を見直すべきかもしれません
3. 本当に汎用的なら、ここ（`Shared/Service/`）に配置します

## 💡 配置例

### 例1: パスワード強度チェックサービス

```php
<?php

namespace App\Domain\Shared\Service;

use App\Domain\Shared\Exception\DomainException;

/**
 * 複数のAggregateで共有されるビジネスルール
 * 
 * User、Admin、Guest など、パスワードを持つすべてのAggregateで使用可能
 * 
 * NOTE: ビジネスルール（パスワード強度要件）を含むため Domain 層に配置
 *       純粋なハッシュ化（技術的実装）は Infrastructure 層
 */
class PasswordStrengthPolicy
{
    /**
     * パスワード強度をチェック
     * 
     * ビジネスルール:
     * - 最低8文字
     * - 大文字、小文字、数字を含む
     */
    public function validate(string $plainPassword): void
    {
        if (!$this->isStrong($plainPassword)) {
            throw new DomainException(
                "パスワードは8文字以上で、大文字、小文字、数字を含む必要があります"
            );
        }
    }
    
    /**
     * パスワードが強度要件を満たすかチェック
     */
    public function isStrong(string $password): bool
    {
        return strlen($password) >= 8
            && preg_match('/[A-Z]/', $password)
            && preg_match('/[a-z]/', $password)
            && preg_match('/[0-9]/', $password);
    }
    
    /**
     * パスワード強度スコアを計算（0-100）
     * 
     * より複雑なビジネスルール
     */
    public function calculateStrength(string $password): int
    {
        $score = 0;
        
        if (strlen($password) >= 8) $score += 25;
        if (strlen($password) >= 12) $score += 15;
        if (preg_match('/[A-Z]/', $password)) $score += 20;
        if (preg_match('/[a-z]/', $password)) $score += 20;
        if (preg_match('/[0-9]/', $password)) $score += 20;
        if (preg_match('/[^A-Za-z0-9]/', $password)) $score += 20;
        
        return min($score, 100);
    }
}
```

**使用例（Application層）:**
```php
class UpdateUserPasswordService
{
    public function __construct(
        private PasswordStrengthPolicy $passwordPolicy,  // Domain Service
        private PasswordHasher $passwordHasher           // Infrastructure Service
    ) {}
    
    public function handle(int $userId, string $newPassword): void
    {
        // 1. ビジネスルールチェック（Domain層）
        $this->passwordPolicy->validate($newPassword);
        
        // 2. 技術的なハッシュ化（Infrastructure層）
        $hashedPassword = $this->passwordHasher->hash($newPassword);
        
        // 3. 保存
        $user->changePassword($hashedPassword);
        $this->repository->save($user);
    }
}
```

### 例2: ID生成ポリシーサービス

```php
<?php

namespace App\Domain\Shared\Service;

use Ramsey\Uuid\Uuid;

/**
 * 全Aggregate共通のID生成ビジネスルール
 * 
 * NOTE: ID生成に「ビジネスルール」が含まれる場合にDomain層に配置
 *       例: 「特定のプレフィックス必須」「連番ルール」など
 *       純粋なUUID生成だけなら Infrastructure層でもよい
 */
class EntityIdGenerator
{
    /**
     * Aggregate種別ごとにプレフィックス付きIDを生成
     * 
     * ビジネスルール:
     * - User: user_xxx
     * - Project: proj_xxx
     * - Todo: todo_xxx
     * - グローバルに一意
     */
    public function generateForAggregate(string $aggregateType): string
    {
        $prefix = $this->getPrefixForAggregate($aggregateType);
        $uuid = Uuid::uuid4()->toString();
        
        return "{$prefix}_{$uuid}";
    }
    
    /**
     * Aggregate種別に応じたプレフィックスを取得
     * 
     * ビジネスルール: IDの可読性とトレーサビリティのため
     */
    private function getPrefixForAggregate(string $aggregateType): string
    {
        return match($aggregateType) {
            'User' => 'user',
            'Project' => 'proj',
            'Todo' => 'todo',
            default => throw new \InvalidArgumentException("Unknown aggregate: {$aggregateType}")
        };
    }
    
    /**
     * IDの妥当性を検証
     */
    public function isValid(string $id): bool
    {
        // プレフィックス_UUID形式かチェック
        if (!preg_match('/^(user|proj|todo)_[0-9a-f-]{36}$/', $id)) {
            return false;
        }
        
        // UUID部分の妥当性をチェック
        $uuidPart = substr($id, strpos($id, '_') + 1);
        return Uuid::isValid($uuidPart);
    }
}
```

### 例3: 異なる種類のAggregateをまたがるサービス（将来の拡張例）

```php
<?php

namespace App\Domain\Shared\Service;

use App\Domain\ProjectAggregate\Repository\ProjectRepositoryInterface;
use App\Domain\ProjectAggregate\ValueObject\ProjectId;
use App\Domain\Shared\Exception\DomainException;
use App\Domain\UserAggregate\Repository\UserRepositoryInterface;
use App\Domain\UserAggregate\ValueObject\UserId;

/**
 * UserとProject、2つの異なる種類のAggregateをまたがる
 */
class UserProjectAssignmentService
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private ProjectRepositoryInterface $projectRepository
    ) {}

    /**
     * ユーザーをプロジェクトにアサイン
     * 
     * ビジネスルール:
     * - ユーザーは同時に5つまでのプロジェクトに参加可能
     * - プロジェクトは最大10人まで
     */
    public function assign(UserId $userId, ProjectId $projectId): void
    {
        $user = $this->userRepository->findById($userId);
        $project = $this->projectRepository->findById($projectId);
        
        if (!$user || !$project) {
            throw new DomainException("ユーザーまたはプロジェクトが見つかりません");
        }
        
        // ドメインルール: ユーザーは5プロジェクトまで
        if ($user->getProjectCount() >= 5) {
            throw new DomainException("ユーザーは最大5つまでのプロジェクトに参加できます");
        }
        
        // ドメインルール: プロジェクトは10人まで
        if ($project->getMemberCount() >= 10) {
            throw new DomainException("プロジェクトは最大10人までです");
        }
        
        // 両方のAggregateを更新
        $user->assignToProject($projectId);
        $project->addMember($userId);
        
        $this->userRepository->save($user);
        $this->projectRepository->save($project);
    }
}
```

## 🚫 配置すべきでないもの

- ❌ **特定のAggregateに特化したロジック** → `Domain/{Aggregate}/Service/` へ
- ❌ **Application Service**（ユースケース実装） → `Application/` へ
- ❌ **Entity内で完結するロジック** → Entity のメソッドとして実装
- ❌ **Infrastructure層の実装** → `Infrastructure/` へ

## 📚 参考

### Aggregate境界の判断基準

| 状況 | 対処 | 配置先 |
|------|------|--------|
| **単一のAggregate内**の操作 | Aggregate Rootのメソッドを使用 | Entity内 |
| **特定のAggregate型に特化**したロジック | Domain Serviceを作成 | `Domain/{Aggregate}/Service/` |
| **どのAggregateでも使える**汎用ロジック | Shared Domain Serviceを作成 | **このディレクトリ** |
| **異なる種類のAggregate**を操作 | Shared Domain Serviceを作成 | **このディレクトリ** |

### 複数Aggregateを操作する際の注意点

```php
// ⚠️ トランザクション境界に注意
// 各Aggregateの保存は独立したトランザクション

// ❌ 悪い例: 1つ目が成功して2つ目が失敗すると不整合
$this->userRepository->save($user1);
$this->userRepository->save($user2); // ここで失敗したらuser1だけ保存される

// ✅ 良い例: Application層でトランザクション管理
// Application Serviceで DB::transaction() を使用
DB::transaction(function () use ($service, $user1Id, $user2Id, $todoId) {
    $service->transfer($user1Id, $user2Id, $todoId);
});
```

### レイヤーの使い分け（Domain vs Infrastructure）

この疑問に答えるために、レイヤーの使い分けを明確にします：

#### Domain層に配置すべきもの
```php
// ✅ ビジネスルール（パスワード強度要件）を含む
class PasswordStrengthPolicy  // Domain/Shared/Service/
{
    public function validate(string $password): void
    {
        // ビジネスルール: 8文字以上、大文字小文字数字必須
        if (strlen($password) < 8) {
            throw new DomainException("パスワードは8文字以上必要");
        }
    }
}
```

#### Infrastructure層に配置すべきもの
```php
// ✅ 純粋な技術的実装（ビジネスルール無し）
class PasswordHasher  // Infrastructure/Shared/
{
    public function hash(string $password): string
    {
        // 技術的な実装のみ
        return password_hash($password, PASSWORD_BCRYPT);
    }
}
```

#### 判断基準

| 質問 | YES → Domain層 | NO → Infrastructure層 |
|------|----------------|----------------------|
| **ビジネスルールを含む？** | `Domain/Shared/Service/` | `Infrastructure/` |
| **業務要件に基づく？** | `Domain/Shared/Service/` | `Infrastructure/` |
| **純粋な技術的実装？** | - | `Infrastructure/` ✅ |

### いつ作成するか？

以下の状況になったら、Shared Domain Serviceの作成を検討してください：

1. **異なる種類のAggregate**を同時に操作する必要がある
2. **複数のAggregateで共有されるビジネスルール**がある
3. **Aggregate間の整合性チェック**が必要
4. ただし、**ビジネスルールを含まない純粋な技術的実装**なら `Infrastructure/` へ

それまでは、このディレクトリは空のままで問題ありません。

## 💡 現在のアプリケーション

このアプリケーションでは、すべてのTodoがUser Aggregateに所有されており、User経由でのみ操作されます。
そのため、現時点では**異なる種類のAggregateをまたがる操作**は存在せず、このディレクトリは使用されていません。

### 将来の使用例

以下のような**異なる種類のAggregate**が追加された場合に使用を検討してください：

#### ✅ Shared/Service/ に配置すべき例
- **UserとProjectの協調**（異なるAggregate）
  - 例: `UserProjectAssignmentService` - ユーザーをプロジェクトに割り当てる
- **UserとTeamの協調**（異なるAggregate）
  - 例: `UserTeamMembershipService` - チームメンバー管理
- **ProjectとTaskの協調**（異なるAggregate）
  - 例: `ProjectTaskAllocationService` - プロジェクトにタスクを割り当て

#### ❌ Shared/Service/ に配置すべきでない例
- ~~**Todo移譲機能**（UserA → UserB）~~ 
  - → `Domain/UserAggregate/Service/` に配置（同じAggregate型）
- ~~**Todo共有機能**（コピー）~~
  - → `Domain/UserAggregate/Service/` に配置（同じAggregate型）
