<?php

namespace Tests\Unit\Domain\UserAggregate\ValueObject;

use App\Domain\UserAggregate\ValueObject\Email;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class EmailTest extends TestCase
{
    public function test_valid_email(): void
    {
        $email = new Email('test@example.com');
        
        $this->assertSame('test@example.com', $email->getValue());
        $this->assertSame('test@example.com', (string) $email);
    }

    public function test_empty_email_throws_exception(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('メールアドレスは空にできません');
        
        new Email('');
    }

    public function test_invalid_email_throws_exception(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('有効なメールアドレスを入力してください');
        
        new Email('invalid-email');
    }

    public function test_invalid_email_without_domain_throws_exception(): void
    {
        $this->expectException(InvalidArgumentException::class);
        
        new Email('test@');
    }

    public function test_equals(): void
    {
        $email1 = new Email('test@example.com');
        $email2 = new Email('test@example.com');
        $email3 = new Email('other@example.com');

        $this->assertTrue($email1->equals($email2));
        $this->assertFalse($email1->equals($email3));
    }
}

