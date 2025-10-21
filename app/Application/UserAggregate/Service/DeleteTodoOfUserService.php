<?php

namespace App\Application\UserAggregate\Service;

use App\Domain\UserAggregate\Repository\UserRepositoryInterface;
use App\Domain\UserAggregate\ValueObject\TodoId;
use App\Domain\UserAggregate\ValueObject\UserId;
use RuntimeException;

/**
 * ユースケース: ユーザーのTodoを削除する
 * 
 * アクター: ユーザー
 * 目的: 不要なタスクを削除する
 * 
 * メインフロー:
 * 1. ユーザーが「削除」ボタンをクリック
 * 2. システムが確認ダイアログを表示
 * 3. ユーザーが削除を確認
 * 4. システムがUserを取得
 * 5. UserからTodoを削除
 * 6. システムがUserを保存
 * 
 * 事前条件:
 * - ユーザーが認証済み
 * - Todoがユーザーに属している
 * 
 * 事後条件:
 * - Todoがデータベースから削除されている
 */
class DeleteTodoOfUserService
{
    private UserRepositoryInterface $repository;

    public function __construct(UserRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * ユーザーのTodoを削除
     * 
     * @throws RuntimeException Userが見つからない場合
     */
    public function handle(int $userId, int $todoId): void
    {
        // Userを取得
        $user = $this->repository->findById(new UserId($userId));
        
        if (!$user) {
            throw new RuntimeException("ユーザーが見つかりません（ID: {$userId}）");
        }

        // UserからTodoを削除
        $user->removeTodo(new TodoId($todoId));

        // Userを保存
        $this->repository->save($user);
    }
}

