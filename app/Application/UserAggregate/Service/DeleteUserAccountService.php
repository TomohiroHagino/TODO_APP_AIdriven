<?php

namespace App\Application\UserAggregate\Service;

use App\Domain\UserAggregate\Repository\UserRepositoryInterface;
use App\Domain\UserAggregate\ValueObject\UserId;
use RuntimeException;

/**
 * ユーザーアカウント削除サービス
 * 
 * ユースケース: ユーザーが自分のアカウントを削除する
 */
class DeleteUserAccountService
{
    private UserRepositoryInterface $repository;

    public function __construct(UserRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * アカウントを削除
     * 
     * User Aggregateの原則により、Userを削除すると
     * 所有するすべてのTodoも自動的に削除される（CASCADE）
     *
     * @param int $userId ユーザーID
     * @return void
     * @throws RuntimeException ユーザーが見つからない場合
     */
    public function handle(int $userId): void
    {
        $user = $this->repository->findById(new UserId($userId));

        if (!$user) {
            throw new RuntimeException("ユーザーが見つかりません（ID: {$userId}）");
        }

        $this->repository->delete($user->getId());
    }
}

