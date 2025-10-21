<?php

namespace Tests\Unit\Domain\UserAggregate\ValueObject;

use App\Domain\UserAggregate\ValueObject\TaskStatus;
use PHPUnit\Framework\TestCase;

class TaskStatusTest extends TestCase
{
    public function test_pending_status(): void
    {
        $status = TaskStatus::pending();
        
        $this->assertFalse($status->isDone());
        $this->assertTrue($status->isPending());
        $this->assertSame('未完了', (string) $status);
    }

    public function test_done_status(): void
    {
        $status = TaskStatus::done();
        
        $this->assertTrue($status->isDone());
        $this->assertFalse($status->isPending());
        $this->assertSame('完了', (string) $status);
    }

    public function test_from_bool_false(): void
    {
        $status = TaskStatus::fromBool(false);
        
        $this->assertTrue($status->isPending());
    }

    public function test_from_bool_true(): void
    {
        $status = TaskStatus::fromBool(true);
        
        $this->assertTrue($status->isDone());
    }

    public function test_toggle_from_pending_to_done(): void
    {
        $pending = TaskStatus::pending();
        $done = $pending->toggle();
        
        $this->assertTrue($done->isDone());
        // 元のオブジェクトは変更されない（不変性）
        $this->assertTrue($pending->isPending());
    }

    public function test_toggle_from_done_to_pending(): void
    {
        $done = TaskStatus::done();
        $pending = $done->toggle();
        
        $this->assertTrue($pending->isPending());
        // 元のオブジェクトは変更されない（不変性）
        $this->assertTrue($done->isDone());
    }

    public function test_equals(): void
    {
        $pending1 = TaskStatus::pending();
        $pending2 = TaskStatus::pending();
        $done = TaskStatus::done();

        $this->assertTrue($pending1->equals($pending2));
        $this->assertFalse($pending1->equals($done));
    }
}

