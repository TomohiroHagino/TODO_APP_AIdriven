<?php

namespace App\Application\UserAggregate\Service;

use App\Domain\UserAggregate\Repository\UserRepositoryInterface;
use App\Domain\UserAggregate\ValueObject\TodoId;
use App\Domain\UserAggregate\ValueObject\UserId;
use RuntimeException;

/**
 * ユースケース: Todoのステータスを切り替える
 * 
 * アクター: ユーザー
 * 目的: タスクの完了/未完了を切り替える
 * 
 * メインフロー:
 * 1. ユーザーが「完了/未完了」ボタンをクリック
 * 2. システムがUserを取得
 * 3. UserからTodoを検索
 * 4. Todoのステータスを切り替え
 * 5. システムがUserを保存
 * 
 * 事前条件:
 * - ユーザーが認証済み
 * - Todoがユーザーに属している
 * 
 * 事後条件:
 * - Todoのステータスが反転している
 */
class ToggleTodoStatusService
{
    private UserRepositoryInterface $repository;

    public function __construct(UserRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Todoのステータスを切り替え
     * 
     * @throws RuntimeException UserまたはTodoが見つからない場合
     */
    public function handle(int $userId, int $todoId): void
    {
        // Userを取得
        $user = $this->repository->findById(new UserId($userId));
        
        if (!$user) {
            throw new RuntimeException("ユーザーが見つかりません（ID: {$userId}）");
        }

        // UserからTodoを検索
        $todo = $user->findTodo(new TodoId($todoId));
        
        if (!$todo) {
            throw new RuntimeException("Todoが見つかりません（ID: {$todoId}）");
        }

        // ステータスを切り替え
        $todo->toggleStatus();

        // Userを保存
        $this->repository->save($user);
    }
}

