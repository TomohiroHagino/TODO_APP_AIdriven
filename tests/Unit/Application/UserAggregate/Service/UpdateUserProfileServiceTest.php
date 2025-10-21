<?php

namespace Tests\Unit\Application\UserAggregate\Service;

use App\Application\UserAggregate\Service\UpdateUserProfileService;
use App\Domain\Shared\ValueObject\DateTimeValue;
use App\Domain\UserAggregate\Entity\UserEntity;
use App\Domain\UserAggregate\Repository\UserRepositoryInterface;
use App\Domain\UserAggregate\ValueObject\Email;
use App\Domain\UserAggregate\ValueObject\UserId;
use App\Domain\UserAggregate\ValueObject\UserName;
use Mockery;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class UpdateUserProfileServiceTest extends TestCase
{
    private UserRepositoryInterface $repository;
    private UpdateUserProfileService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = Mockery::mock(UserRepositoryInterface::class);
        $this->service = new UpdateUserProfileService($this->repository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_プロフィール情報を更新できる(): void
    {
        $userId = 1;
        $user = new UserEntity(
            new UserId($userId),
            new UserName('旧名前'),
            new Email('old@example.com'),
            'hashed_password',
            DateTimeValue::now()
        );

        $this->repository
            ->shouldReceive('findById')
            ->once()
            ->with(Mockery::on(fn($id) => $id->getValue() === $userId))
            ->andReturn($user);

        $this->repository
            ->shouldReceive('existsByEmail')
            ->once()
            ->with(Mockery::on(fn($email) => $email->getValue() === 'new@example.com'))
            ->andReturn(false);

        $this->repository
            ->shouldReceive('save')
            ->once()
            ->with(Mockery::on(function ($savedUser) {
                return $savedUser->getName()->getValue() === '新名前' &&
                       $savedUser->getEmail()->getValue() === 'new@example.com';
            }));

        $this->service->handle($userId, '新名前', 'new@example.com');

        $this->assertTrue(true); // アサーションが呼ばれたことを確認
    }

    public function test_ユーザーが見つからない場合は例外をスローする(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('ユーザーが見つかりません（ID: 999）');

        $this->repository
            ->shouldReceive('findById')
            ->once()
            ->andReturn(null);

        $this->service->handle(999, '名前', 'email@example.com');
    }

    public function test_メールアドレスが重複している場合は例外をスローする(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('このメールアドレスは既に使用されています');

        $userId = 1;
        $user = new UserEntity(
            new UserId($userId),
            new UserName('名前'),
            new Email('old@example.com'),
            'hashed_password',
            DateTimeValue::now()
        );

        $this->repository
            ->shouldReceive('findById')
            ->once()
            ->andReturn($user);

        $this->repository
            ->shouldReceive('existsByEmail')
            ->once()
            ->with(Mockery::on(fn($email) => $email->getValue() === 'duplicate@example.com'))
            ->andReturn(true);

        $this->service->handle($userId, '名前', 'duplicate@example.com');
    }

    public function test_メールアドレスが変更されない場合は重複チェックをスキップする(): void
    {
        $userId = 1;
        $email = 'same@example.com';
        $user = new UserEntity(
            new UserId($userId),
            new UserName('旧名前'),
            new Email($email),
            'hashed_password',
            DateTimeValue::now()
        );

        $this->repository
            ->shouldReceive('findById')
            ->once()
            ->andReturn($user);

        // existsByEmailは呼ばれないことを期待
        $this->repository
            ->shouldNotReceive('existsByEmail');

        $this->repository
            ->shouldReceive('save')
            ->once()
            ->with(Mockery::on(function ($savedUser) {
                return $savedUser->getName()->getValue() === '新名前' &&
                       $savedUser->getEmail()->getValue() === 'same@example.com';
            }));

        $this->service->handle($userId, '新名前', $email);

        $this->assertTrue(true);
    }
}

