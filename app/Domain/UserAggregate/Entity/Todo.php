<?php

namespace App\Domain\UserAggregate\Entity;

use App\Domain\Shared\ValueObject\DateTimeValue;
use App\Domain\UserAggregate\ValueObject\TaskStatus;
use App\Domain\UserAggregate\ValueObject\TaskTitle;
use App\Domain\UserAggregate\ValueObject\TodoId;
use App\Domain\UserAggregate\ValueObject\UserId;

/**
 * Todoエンティティ（子エンティティ）
 * 
 * Userに所有される子エンティティ
 * User Aggregateの一部として存在
 */
class Todo
{
    private TodoId $id;
    private UserId $userId;
    private TaskTitle $title;
    private TaskStatus $status;
    private DateTimeValue $createdAt;

    public function __construct(
        TodoId $id,
        UserId $userId,
        TaskTitle $title,
        TaskStatus $status,
        DateTimeValue $createdAt
    ) {
        $this->id = $id;
        $this->userId = $userId;
        $this->title = $title;
        $this->status = $status;
        $this->createdAt = $createdAt;
    }

    /**
     * 新しいTodoを作成
     */
    public static function create(
        TodoId $id,
        UserId $userId,
        TaskTitle $title
    ): self {
        return new self(
            $id,
            $userId,
            $title,
            TaskStatus::pending(),
            DateTimeValue::now()
        );
    }

    // Getters

    public function getId(): TodoId
    {
        return $this->id;
    }

    public function getUserId(): UserId
    {
        return $this->userId;
    }

    public function getTitle(): TaskTitle
    {
        return $this->title;
    }

    public function getStatus(): TaskStatus
    {
        return $this->status;
    }

    public function getCreatedAt(): DateTimeValue
    {
        return $this->createdAt;
    }

    // Business logic

    /**
     * タイトルを変更
     */
    public function changeTitle(TaskTitle $title): void
    {
        $this->title = $title;
    }

    /**
     * ステータスを切り替え
     */
    public function toggleStatus(): void
    {
        $this->status = $this->status->toggle();
    }

    /**
     * タスクが完了しているか
     */
    public function isDone(): bool
    {
        return $this->status->isDone();
    }

    /**
     * タスクが未完了か
     */
    public function isPending(): bool
    {
        return $this->status->isPending();
    }
}

