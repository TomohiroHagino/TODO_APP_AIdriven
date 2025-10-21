<?php

namespace App\Application\UserAggregate\Service;

use App\Domain\UserAggregate\Repository\UserRepositoryInterface;
use App\Domain\UserAggregate\ValueObject\UserId;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

/**
 * ユーザーパスワード更新サービス
 * 
 * ユースケース: ユーザーが自分のパスワードを更新する
 */
class UpdateUserPasswordService
{
    private UserRepositoryInterface $repository;

    public function __construct(UserRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * パスワードを更新
     *
     * @param int $userId ユーザーID
     * @param string $currentPassword 現在のパスワード
     * @param string $newPassword 新しいパスワード
     * @return void
     * @throws RuntimeException ユーザーが見つからない、または現在のパスワードが間違っている場合
     */
    public function handle(int $userId, string $currentPassword, string $newPassword): void
    {
        $user = $this->repository->findById(new UserId($userId));

        if (!$user) {
            throw new RuntimeException("ユーザーが見つかりません（ID: {$userId}）");
        }

        // 現在のパスワードを検証
        if (!Hash::check($currentPassword, $user->getPassword())) {
            throw new RuntimeException("現在のパスワードが正しくありません");
        }

        // 新しいパスワードをハッシュ化して設定
        $user->changePassword(Hash::make($newPassword));

        $this->repository->save($user);
    }
}

