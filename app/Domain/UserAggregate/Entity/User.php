<?php

namespace App\Domain\UserAggregate\Entity;

use App\Domain\Shared\ValueObject\DateTimeValue;
use App\Domain\UserAggregate\ValueObject\Email;
use App\Domain\UserAggregate\ValueObject\TaskTitle;
use App\Domain\UserAggregate\ValueObject\TodoId;
use App\Domain\UserAggregate\ValueObject\UserId;
use App\Domain\UserAggregate\ValueObject\UserName;

/**
 * Userエンティティ（Aggregate Root）
 * 
 * UserAggregateの集約ルート
 * Todoを所有し、Todoへのすべての操作はUser経由で行う
 */
class User
{
    private UserId $id;
    private UserName $name;
    private Email $email;
    private string $password;
    private DateTimeValue $createdAt;
    
    /** @var Todo[] */
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
     * 新しいUserを作成
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
     * @return Todo[]
     */
    public function getTodos(): array
    {
        return $this->todos;
    }

    // Todo management (Aggregate Root の責務)

    /**
     * Todoを追加
     */
    public function addTodo(TodoId $todoId, TaskTitle $title): Todo
    {
        $todo = Todo::create($todoId, $this->id, $title);
        $this->todos[] = $todo;
        
        return $todo;
    }

    /**
     * Todoを設定（リポジトリから復元時に使用）
     */
    public function setTodos(array $todos): void
    {
        $this->todos = $todos;
    }

    /**
     * 特定のTodoを取得
     */
    public function findTodo(TodoId $todoId): ?Todo
    {
        foreach ($this->todos as $todo) {
            if ($todo->getId()->equals($todoId)) {
                return $todo;
            }
        }
        return null;
    }

    /**
     * Todoを削除
     */
    public function removeTodo(TodoId $todoId): void
    {
        $this->todos = array_values(array_filter(
            $this->todos,
            fn(Todo $todo) => !$todo->getId()->equals($todoId)
        ));
    }

    /**
     * 完了したTodoのみを取得
     * 
     * @return Todo[]
     */
    public function getDoneTodos(): array
    {
        return array_filter(
            $this->todos,
            fn(Todo $todo) => $todo->isDone()
        );
    }

    /**
     * 未完了のTodoのみを取得
     * 
     * @return Todo[]
     */
    public function getPendingTodos(): array
    {
        return array_filter(
            $this->todos,
            fn(Todo $todo) => $todo->isPending()
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

