<?php

namespace App\Domain\UserAggregate\ValueObject;

use InvalidArgumentException;

/**
 * タスクタイトル値オブジェクト
 */
class TaskTitle
{
    private string $value;

    public function __construct(string $value)
    {
        if (empty(trim($value))) {
            throw new InvalidArgumentException('タスクタイトルは空にできません');
        }

        if (mb_strlen($value) > 255) {
            throw new InvalidArgumentException('タスクタイトルは255文字以内である必要があります');
        }

        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function equals(TaskTitle $other): bool
    {
        return $this->value === $other->getValue();
    }

    public function __toString(): string
    {
        return $this->value;
    }
}

