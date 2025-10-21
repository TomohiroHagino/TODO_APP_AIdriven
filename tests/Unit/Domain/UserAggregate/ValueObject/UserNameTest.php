<?php

namespace Tests\Unit\Domain\UserAggregate\ValueObject;

use App\Domain\UserAggregate\ValueObject\UserName;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class UserNameTest extends TestCase
{
    public function test_valid_name(): void
    {
        $name = new UserName('山田太郎');
        
        $this->assertSame('山田太郎', $name->getValue());
        $this->assertSame('山田太郎', (string) $name);
    }

    public function test_empty_name_throws_exception(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('ユーザー名は空にできません');
        
        new UserName('');
    }

    public function test_too_long_name_throws_exception(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('ユーザー名は255文字以内である必要があります');
        
        new UserName(str_repeat('あ', 256));
    }

    public function test_equals(): void
    {
        $name1 = new UserName('山田太郎');
        $name2 = new UserName('山田太郎');
        $name3 = new UserName('佐藤花子');

        $this->assertTrue($name1->equals($name2));
        $this->assertFalse($name1->equals($name3));
    }
}

