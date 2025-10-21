<?php

namespace Tests\Unit\Application\UserAggregate\Service;

use App\Application\UserAggregate\Service\AddTodoToUserService;
use App\Domain\Shared\ValueObject\DateTimeValue;
use App\Domain\UserAggregate\Entity\UserEntity;
use App\Domain\UserAggregate\Repository\UserRepositoryInterface;
use App\Domain\UserAggregate\ValueObject\Email;
use App\Domain\UserAggregate\ValueObject\UserId;
use App\Domain\UserAggregate\ValueObject\UserName;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class AddTodoToUserServiceTest extends TestCase
{
    public function test_handle_adds_todo_to_user(): void
    {
        $repository = $this->createMock(UserRepositoryInterface::class);
        
        $user = UserEntity::create(
            new UserId(1),
            new UserName('Test User'),
            new Email('test@example.com'),
            'password'
        );

        $repository->expects($this->once())
            ->method('findById')
            ->with($this->equalTo(new UserId(1)))
            ->willReturn($user);

        $repository->expects($this->once())
            ->method('nextTodoId')
            ->willReturn(1);

        $repository->expects($this->once())
            ->method('save')
            ->with($user);

        $service = new AddTodoToUserService($repository);
        $todo = $service->handle(1, 'New Task');

        $this->assertSame('New Task', $todo->getTitle()->getValue());
        $this->assertTrue($todo->isPending());
    }

    public function test_handle_throws_exception_when_user_not_found(): void
    {
        $repository = $this->createMock(UserRepositoryInterface::class);
        
        $repository->expects($this->once())
            ->method('findById')
            ->willReturn(null);

        $service = new AddTodoToUserService($repository);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('ユーザーが見つかりません');

        $service->handle(999, 'New Task');
    }
}

