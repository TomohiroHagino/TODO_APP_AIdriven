<?php

namespace App\Application\UserAggregate\Service;

use App\Domain\UserAggregate\Entity\TodoEntity;
use App\Domain\UserAggregate\Repository\UserRepositoryInterface;
use App\Domain\UserAggregate\ValueObject\TaskTitle;
use App\Domain\UserAggregate\ValueObject\TodoId;
use App\Domain\UserAggregate\ValueObject\UserId;
use RuntimeException;

/**
 * ユースケース: ユーザーにTodoを追加する
 * 
 * アクター: ユーザー
 * 目的: 新しいタスクを登録する
 * 
 * メインフロー:
 * 1. ユーザーがタスク名を入力
 * 2. システムがタスク名をバリデーション
 * 3. システムが新しいTodoIDを採番
 * 4. システムがUserを取得
 * 5. UserにTodoを追加
 * 6. システムがUserを保存（Todoも一緒に保存される）
 * 
 * 事前条件: 
 * - ユーザーが認証済み
 * - タスク名が1〜255文字
 * 
 * 事後条件: 
 * - 新しいタスクがユーザーに紐づいて永続化されている
 */
class AddTodoToUserService
{
    private UserRepositoryInterface $repository;

    public function __construct(UserRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * ユーザーに新しいTodoを追加
     * 
     * @throws RuntimeException Userが見つからない場合
     */
    public function handle(int $userId, string $title): TodoEntity
    {
        // Userを取得
        $user = $this->repository->findById(new UserId($userId));
        
        if (!$user) {
            throw new RuntimeException("ユーザーが見つかりません（ID: {$userId}）");
        }

        // 新しいTodoIDを採番
        $todoId = new TodoId($this->repository->nextTodoId());

        // UserにTodoを追加
        $todo = $user->addTodo($todoId, new TaskTitle($title));

        // Userを保存（Todoも一緒に保存される）
        $this->repository->save($user);

        return $todo;
    }
}

