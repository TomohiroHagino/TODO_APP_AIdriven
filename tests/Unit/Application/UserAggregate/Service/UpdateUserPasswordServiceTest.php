<?php

namespace Tests\Unit\Application\UserAggregate\Service;

use App\Application\UserAggregate\Service\UpdateUserPasswordService;
use App\Domain\Shared\ValueObject\DateTimeValue;
use App\Domain\UserAggregate\Entity\UserEntity;
use App\Domain\UserAggregate\Repository\UserRepositoryInterface;
use App\Domain\UserAggregate\ValueObject\Email;
use App\Domain\UserAggregate\ValueObject\UserId;
use App\Domain\UserAggregate\ValueObject\UserName;
use Illuminate\Support\Facades\Hash;
use Mockery;
use RuntimeException;
use Tests\TestCase;

class UpdateUserPasswordServiceTest extends TestCase
{
    private UserRepositoryInterface $repository;
    private UpdateUserPasswordService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = Mockery::mock(UserRepositoryInterface::class);
        $this->service = new UpdateUserPasswordService($this->repository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_パスワードを更新できる(): void
    {
        Hash::shouldReceive('check')
            ->once()
            ->with('current_password', 'hashed_current_password')
            ->andReturn(true);

        Hash::shouldReceive('make')
            ->once()
            ->with('new_password')
            ->andReturn('hashed_new_password');

        $userId = 1;
        $user = new UserEntity(
            new UserId($userId),
            new UserName('名前'),
            new Email('user@example.com'),
            'hashed_current_password',
            DateTimeValue::now()
        );

        $this->repository
            ->shouldReceive('findById')
            ->once()
            ->with(Mockery::on(fn($id) => $id->getValue() === $userId))
            ->andReturn($user);

        $this->repository
            ->shouldReceive('save')
            ->once()
            ->with(Mockery::on(function ($savedUser) {
                return $savedUser->getPassword() === 'hashed_new_password';
            }));

        $this->service->handle($userId, 'current_password', 'new_password');

        $this->assertTrue(true);
    }

    public function test_ユーザーが見つからない場合は例外をスローする(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('ユーザーが見つかりません（ID: 999）');

        $this->repository
            ->shouldReceive('findById')
            ->once()
            ->andReturn(null);

        $this->service->handle(999, 'current_password', 'new_password');
    }

    public function test_現在のパスワードが間違っている場合は例外をスローする(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('現在のパスワードが正しくありません');

        Hash::shouldReceive('check')
            ->once()
            ->with('wrong_password', 'hashed_current_password')
            ->andReturn(false);

        $userId = 1;
        $user = new UserEntity(
            new UserId($userId),
            new UserName('名前'),
            new Email('user@example.com'),
            'hashed_current_password',
            DateTimeValue::now()
        );

        $this->repository
            ->shouldReceive('findById')
            ->once()
            ->andReturn($user);

        $this->service->handle($userId, 'wrong_password', 'new_password');
    }
}

