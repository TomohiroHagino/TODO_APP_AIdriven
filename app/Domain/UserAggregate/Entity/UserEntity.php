<?php

namespace App\Domain\UserAggregate\Entity;

use App\Domain\Shared\ValueObject\DateTimeValue;
use App\Domain\UserAggregate\ValueObject\Email;
use App\Domain\UserAggregate\ValueObject\TaskTitle;
use App\Domain\UserAggregate\ValueObject\TodoId;
use App\Domain\UserAggregate\ValueObject\UserId;
use App\Domain\UserAggregate\ValueObject\UserName;

/**
 * UserEntity（Aggregate Root）
 * 
 * UserAggregateの集約ルート
 * TodoEntityを所有し、TodoEntityへのすべての操作はUserEntity経由で行う
 * 
 * NOTE: Eloquent Modelの App\Models\User とは別物です
 */
class UserEntity
{
    private UserId $id;
    private UserName $name;
    private Email $email;
    private string $password;
    private DateTimeValue $createdAt;
    
    /** @var TodoEntity[] */
    private array $todos = [];

    public function __construct(
        UserId $id,
        UserName $name,
        Email $email,
        string $password,
        DateTimeValue $createdAt
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
        $this->createdAt = $createdAt;
    }

    /**
     * 新しいUserEntityを作成
     */
    public static function create(
        UserId $id,
        UserName $name,
        Email $email,
        string $hashedPassword
    ): self {
        return new self(
            $id,
            $name,
            $email,
            $hashedPassword,
            DateTimeValue::now()
        );
    }

    // Getters

    public function getId(): UserId
    {
        return $this->id;
    }

    public function getName(): UserName
    {
        return $this->name;
    }

    public function getEmail(): Email
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getCreatedAt(): DateTimeValue
    {
        return $this->createdAt;
    }

    /**
     * @return TodoEntity[]
     */
    public function getTodos(): array
    {
        return $this->todos;
    }

    // TodoEntity management (Aggregate Root の責務)

    /**
     * TodoEntityを追加
     */
    public function addTodo(TodoId $todoId, TaskTitle $title): TodoEntity
    {
        $todo = TodoEntity::create($todoId, $this->id, $title);
        $this->todos[] = $todo;
        
        return $todo;
    }

    /**
     * TodoEntityを設定（リポジトリから復元時に使用）
     */
    public function setTodos(array $todos): void
    {
        $this->todos = $todos;
    }

    /**
     * 特定のTodoEntityを取得
     */
    public function findTodo(TodoId $todoId): ?TodoEntity
    {
        foreach ($this->todos as $todo) {
            if ($todo->getId()->equals($todoId)) {
                return $todo;
            }
        }
        return null;
    }

    /**
     * TodoEntityを削除
     */
    public function removeTodo(TodoId $todoId): void
    {
        $this->todos = array_values(array_filter(
            $this->todos,
            fn(TodoEntity $todo) => !$todo->getId()->equals($todoId)
        ));
    }

    /**
     * 完了したTodoEntityのみを取得
     * 
     * @return TodoEntity[]
     */
    public function getDoneTodos(): array
    {
        return array_filter(
            $this->todos,
            fn(TodoEntity $todo) => $todo->isDone()
        );
    }

    /**
     * 未完了のTodoEntityのみを取得
     * 
     * @return TodoEntity[]
     */
    public function getPendingTodos(): array
    {
        return array_filter(
            $this->todos,
            fn(TodoEntity $todo) => $todo->isPending()
        );
    }

    // User profile management

    /**
     * 名前を変更
     */
    public function changeName(UserName $name): void
    {
        $this->name = $name;
    }

    /**
     * メールアドレスを変更
     */
    public function changeEmail(Email $email): void
    {
        $this->email = $email;
    }

    /**
     * パスワードを変更
     */
    public function changePassword(string $hashedPassword): void
    {
        $this->password = $hashedPassword;
    }
}

