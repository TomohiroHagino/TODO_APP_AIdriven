<?php

namespace Tests\Unit\Domain\Shared\ValueObject;

use App\Domain\Shared\ValueObject\DateTimeValue;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class DateTimeValueTest extends TestCase
{
    public function test_now_creates_current_datetime(): void
    {
        $before = new DateTimeImmutable();
        $dateTime = DateTimeValue::now();
        $after = new DateTimeImmutable();

        $this->assertGreaterThanOrEqual(
            $before->format('Y-m-d H:i:s'),
            $dateTime->format('Y-m-d H:i:s')
        );
        $this->assertLessThanOrEqual(
            $after->format('Y-m-d H:i:s'),
            $dateTime->format('Y-m-d H:i:s')
        );
    }

    public function test_from_string_creates_datetime(): void
    {
        $dateTime = DateTimeValue::fromString('2024-01-01 12:00:00');
        
        $this->assertEquals('2024-01-01 12:00:00', $dateTime->format());
    }

    public function test_format_with_custom_format(): void
    {
        $dateTime = DateTimeValue::fromString('2024-01-01 12:00:00');
        
        $this->assertEquals('2024/01/01', $dateTime->format('Y/m/d'));
        $this->assertEquals('01-01-2024', $dateTime->format('m-d-Y'));
    }

    public function test_equals_returns_true_for_same_datetime(): void
    {
        $dateTime1 = DateTimeValue::fromString('2024-01-01 12:00:00');
        $dateTime2 = DateTimeValue::fromString('2024-01-01 12:00:00');
        
        $this->assertTrue($dateTime1->equals($dateTime2));
    }

    public function test_equals_returns_false_for_different_datetime(): void
    {
        $dateTime1 = DateTimeValue::fromString('2024-01-01 12:00:00');
        $dateTime2 = DateTimeValue::fromString('2024-01-02 12:00:00');
        
        $this->assertFalse($dateTime1->equals($dateTime2));
    }

    public function test_is_before_returns_true_when_before(): void
    {
        $earlier = DateTimeValue::fromString('2024-01-01 12:00:00');
        $later = DateTimeValue::fromString('2024-01-02 12:00:00');
        
        $this->assertTrue($earlier->isBefore($later));
    }

    public function test_is_before_returns_false_when_after(): void
    {
        $earlier = DateTimeValue::fromString('2024-01-01 12:00:00');
        $later = DateTimeValue::fromString('2024-01-02 12:00:00');
        
        $this->assertFalse($later->isBefore($earlier));
    }

    public function test_is_after_returns_true_when_after(): void
    {
        $earlier = DateTimeValue::fromString('2024-01-01 12:00:00');
        $later = DateTimeValue::fromString('2024-01-02 12:00:00');
        
        $this->assertTrue($later->isAfter($earlier));
    }

    public function test_is_after_returns_false_when_before(): void
    {
        $earlier = DateTimeValue::fromString('2024-01-01 12:00:00');
        $later = DateTimeValue::fromString('2024-01-02 12:00:00');
        
        $this->assertFalse($earlier->isAfter($later));
    }

    public function test_to_string_returns_formatted_string(): void
    {
        $dateTime = DateTimeValue::fromString('2024-01-01 12:00:00');
        
        $this->assertEquals('2024-01-01 12:00:00', (string) $dateTime);
    }

    public function test_get_value_returns_datetime_immutable(): void
    {
        $original = new DateTimeImmutable('2024-01-01 12:00:00');
        $dateTime = new DateTimeValue($original);
        
        $this->assertInstanceOf(DateTimeImmutable::class, $dateTime->getValue());
        $this->assertEquals($original, $dateTime->getValue());
    }
}

