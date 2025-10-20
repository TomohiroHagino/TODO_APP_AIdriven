<?php

namespace Tests\Unit\Domain\Todo;

use PHPUnit\Framework\TestCase;
use App\Domain\Todo\ValueObject\TaskTitle;

class TaskTitleTest extends TestCase
{
    // 正しいタイトルでTaskTitleが生成でき、値がそのままgetValueで取得できることをテスト
    public function test_valid_title()
    {
        $taskTitle = new TaskTitle('掃除をする');
        $this->assertEquals('掃除をする', $taskTitle->getValue());
    }

    // 空文字のタイトルを渡すと例外が発生することをテスト
    public function test_empty_title_throws()
    {
        $this->expectException(\InvalidArgumentException::class);
        new TaskTitle('');
    }

    // 256文字以上のタイトルを渡すと例外が発生することをテスト
    public function test_too_long_title_throws()
    {
        $this->expectException(\InvalidArgumentException::class);
        new TaskTitle(str_repeat('a', 256));
    }
}