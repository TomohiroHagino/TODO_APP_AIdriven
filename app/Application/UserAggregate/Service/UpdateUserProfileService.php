<?php

namespace App\Application\UserAggregate\Service;

use App\Domain\UserAggregate\Repository\UserRepositoryInterface;
use App\Domain\UserAggregate\ValueObject\Email;
use App\Domain\UserAggregate\ValueObject\UserId;
use App\Domain\UserAggregate\ValueObject\UserName;
use RuntimeException;

/**
 * ユーザープロフィール更新サービス
 * 
 * ユースケース: ユーザーが自分のプロフィール（名前・メール）を更新する
 */
class UpdateUserProfileService
{
    private UserRepositoryInterface $repository;

    public function __construct(UserRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * プロフィールを更新
     *
     * @param int $userId ユーザーID
     * @param string $name 新しい名前
     * @param string $email 新しいメールアドレス
     * @return void
     * @throws RuntimeException ユーザーが見つからない、またはメールが重複している場合
     */
    public function handle(int $userId, string $name, string $email): void
    {
        $user = $this->repository->findById(new UserId($userId));

        if (!$user) {
            throw new RuntimeException("ユーザーが見つかりません（ID: {$userId}）");
        }

        $newEmail = new Email($email);

        // メールアドレスが変更された場合、重複チェック
        if (!$user->getEmail()->equals($newEmail)) {
            if ($this->repository->existsByEmail($newEmail)) {
                throw new RuntimeException("このメールアドレスは既に使用されています");
            }
            $user->changeEmail($newEmail);
        }

        // 名前を更新
        $user->changeName(new UserName($name));

        $this->repository->save($user);
    }
}

