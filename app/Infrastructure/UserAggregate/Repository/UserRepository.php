<?php

namespace App\Infrastructure\UserAggregate\Repository;

use App\Domain\Shared\ValueObject\DateTimeValue;
use App\Domain\UserAggregate\Entity\Todo;
use App\Domain\UserAggregate\Entity\User;
use App\Domain\UserAggregate\Repository\UserRepositoryInterface;
use App\Domain\UserAggregate\ValueObject\Email;
use App\Domain\UserAggregate\ValueObject\TaskStatus;
use App\Domain\UserAggregate\ValueObject\TaskTitle;
use App\Domain\UserAggregate\ValueObject\TodoId;
use App\Domain\UserAggregate\ValueObject\UserId;
use App\Domain\UserAggregate\ValueObject\UserName;
use App\Models\TodoModel;
use App\Models\User as UserModel;  // Eloquent Model（Domain層のUserと区別）

/**
 * UserRepository実装（Eloquent）
 * 
 * EloquentモデルとDomainエンティティの変換を担当
 */
class UserRepository implements UserRepositoryInterface
{
    public function nextUserId(): UserId
    {
        $maxId = UserModel::max('id') ?? 0;
        return new UserId($maxId + 1);
    }

    public function nextTodoId(): int
    {
        return (TodoModel::max('id') ?? 0) + 1;
    }

    public function save(User $user): void
    {
        // Userを保存
        $userModel = UserModel::updateOrCreate(
            ['id' => $user->getId()->getValue()],
            [
                'name' => $user->getName()->getValue(),
                'email' => $user->getEmail()->getValue(),
                'password' => $user->getPassword(),
            ]
        );

        // 既存のTodoを全て取得
        $existingTodoIds = $userModel->todos()->pluck('id')->toArray();
        
        // 現在のTodoのIDリスト
        $currentTodoIds = [];

        // Userに紐づくTodosを保存
        foreach ($user->getTodos() as $todo) {
            TodoModel::updateOrCreate(
                ['id' => $todo->getId()->getValue()],
                [
                    'user_id' => $user->getId()->getValue(),
                    'title' => $todo->getTitle()->getValue(),
                    'is_done' => $todo->getStatus()->isDone(),
                    'created_at' => $todo->getCreatedAt()->format('Y-m-d H:i:s'),
                ]
            );
            $currentTodoIds[] = $todo->getId()->getValue();
        }

        // Userから削除されたTodoをDBからも削除
        $deletedTodoIds = array_diff($existingTodoIds, $currentTodoIds);
        if (!empty($deletedTodoIds)) {
            TodoModel::whereIn('id', $deletedTodoIds)->delete();
        }
    }

    public function findById(UserId $userId): ?User
    {
        $userModel = UserModel::with('todos')->find($userId->getValue());
        
        if (!$userModel) {
            return null;
        }

        return $this->toDomain($userModel);
    }

    public function findByEmail(Email $email): ?User
    {
        $userModel = UserModel::with('todos')
            ->where('email', $email->getValue())
            ->first();
        
        if (!$userModel) {
            return null;
        }

        return $this->toDomain($userModel);
    }

    public function findAll(): array
    {
        $userModels = UserModel::with('todos')->get();
        
        return $userModels->map(function ($userModel) {
            return $this->toDomain($userModel);
        })->toArray();
    }

    public function delete(UserId $userId): void
    {
        UserModel::destroy($userId->getValue());
        // TodoはCascade削除される
    }

    public function existsByEmail(Email $email): bool
    {
        return UserModel::where('email', $email->getValue())->exists();
    }

    /**
     * EloquentモデルからDomainエンティティへ変換
     */
    private function toDomain(UserModel $userModel): User
    {
        $user = new User(
            new UserId($userModel->id),
            new UserName($userModel->name),
            new Email($userModel->email),
            $userModel->password,
            DateTimeValue::fromString($userModel->created_at)
        );

        // Todosを変換してUserにセット
        $todos = $userModel->todos->map(function ($todoModel) use ($userModel) {
            return new Todo(
                new TodoId($todoModel->id),
                new UserId($userModel->id),
                new TaskTitle($todoModel->title),
                TaskStatus::fromBool($todoModel->is_done),
                DateTimeValue::fromString($todoModel->created_at)
            );
        })->toArray();

        $user->setTodos($todos);

        return $user;
    }
}

