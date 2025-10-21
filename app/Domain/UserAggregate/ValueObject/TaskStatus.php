<?php

namespace App\Domain\UserAggregate\ValueObject;

/**
 * タスクステータス値オブジェクト
 */
class TaskStatus
{
    private bool $isDone;

    private function __construct(bool $isDone)
    {
        $this->isDone = $isDone;
    }

    public static function pending(): self
    {
        return new self(false);
    }

    public static function done(): self
    {
        return new self(true);
    }

    public static function fromBool(bool $isDone): self
    {
        return new self($isDone);
    }

    public function isDone(): bool
    {
        return $this->isDone;
    }

    public function isPending(): bool
    {
        return !$this->isDone;
    }

    public function toggle(): self
    {
        return new self(!$this->isDone);
    }

    public function equals(TaskStatus $other): bool
    {
        return $this->isDone === $other->isDone();
    }

    public function __toString(): string
    {
        return $this->isDone ? '完了' : '未完了';
    }
}

