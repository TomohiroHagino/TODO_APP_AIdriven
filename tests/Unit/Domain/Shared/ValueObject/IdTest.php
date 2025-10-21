<?php

namespace Tests\Unit\Domain\Shared\ValueObject;

use App\Domain\UserAggregate\ValueObject\UserId;
use App\Domain\UserAggregate\ValueObject\TodoId;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class IdTest extends TestCase
{
    public function test_valid_id_can_be_created(): void
    {
        $userId = new UserId(1);
        
        $this->assertEquals(1, $userId->getValue());
    }

    public function test_throws_exception_for_zero_id(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('IDは1以上の整数である必要があります');
        
        new UserId(0);
    }

    public function test_throws_exception_for_negative_id(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('IDは1以上の整数である必要があります');
        
        new UserId(-1);
    }

    public function test_equals_returns_true_for_same_id_and_type(): void
    {
        $userId1 = new UserId(1);
        $userId2 = new UserId(1);
        
        $this->assertTrue($userId1->equals($userId2));
    }

    public function test_equals_returns_false_for_different_id(): void
    {
        $userId1 = new UserId(1);
        $userId2 = new UserId(2);
        
        $this->assertFalse($userId1->equals($userId2));
    }

    public function test_equals_returns_false_for_different_type(): void
    {
        $userId = new UserId(1);
        $todoId = new TodoId(1);
        
        // 値は同じだが型が違うのでfalse
        $this->assertFalse($userId->equals($todoId));
    }

    public function test_to_string_returns_string_value(): void
    {
        $userId = new UserId(123);
        
        $this->assertEquals('123', (string) $userId);
    }
}

