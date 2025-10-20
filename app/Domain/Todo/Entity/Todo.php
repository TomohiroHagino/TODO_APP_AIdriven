<?php

namespace App\Domain\Todo\Entity;

use App\Domain\Todo\ValueObject\TaskTitle;

class Todo
{
    private int $id;
    private TaskTitle $title;
    private bool $isDone;
    private \DateTimeImmutable $createdAt;

    public function __construct(int $id, TaskTitle $title, bool $isDone, \DateTimeImmutable $createdAt)
    {
        $this->id = $id;
        $this->title = $title;
        $this->isDone = $isDone;
        $this->createdAt = $createdAt;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): TaskTitle
    {
        return $this->title;
    }

    public function isDone(): bool
    {
        return $this->isDone;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function changeTitle(\App\Domain\Todo\ValueObject\TaskTitle $title): void
    {
        $this->title = $title;
    }

    /**
     * 完了/未完了状態を切り替え
     */
    public function toggleStatus(): void
    {
        $this->isDone = !$this->isDone;
    }
}
