<?php

namespace Tests\Unit\Domain\UserAggregate\ValueObject;

use App\Domain\UserAggregate\ValueObject\TaskTitle;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class TaskTitleTest extends TestCase
{
    public function test_valid_title(): void
    {
        $title = new TaskTitle('買い物に行く');
        
        $this->assertSame('買い物に行く', $title->getValue());
        $this->assertSame('買い物に行く', (string) $title);
    }

    public function test_empty_title_throws_exception(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('タスクタイトルは空にできません');
        
        new TaskTitle('');
    }

    public function test_whitespace_only_title_throws_exception(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('タスクタイトルは空にできません');
        
        new TaskTitle('   ');
    }

    public function test_too_long_title_throws_exception(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('タスクタイトルは255文字以内である必要があります');
        
        new TaskTitle(str_repeat('あ', 256));
    }

    public function test_max_length_title_is_valid(): void
    {
        $title = new TaskTitle(str_repeat('あ', 255));
        
        $this->assertSame(str_repeat('あ', 255), $title->getValue());
    }

    public function test_equals(): void
    {
        $title1 = new TaskTitle('買い物');
        $title2 = new TaskTitle('買い物');
        $title3 = new TaskTitle('掃除');

        $this->assertTrue($title1->equals($title2));
        $this->assertFalse($title1->equals($title3));
    }
}

