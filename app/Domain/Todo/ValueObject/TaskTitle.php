<?php

namespace App\Domain\Todo\ValueObject;

class TaskTitle
{
    private string $value;

    public function __construct(string $value)
    {
        $value = trim($value);
        if ($value === '' || mb_strlen($value) > 255) {
            throw new \InvalidArgumentException('タイトルは1文字以上255文字以下で入力してください');
        }
        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }
    
    public function __toString(): string
    {
        return $this->value;
    }
}
