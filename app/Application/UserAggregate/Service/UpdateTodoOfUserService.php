<?php

namespace App\Application\UserAggregate\Service;

use App\Domain\UserAggregate\Repository\UserRepositoryInterface;
use App\Domain\UserAggregate\ValueObject\TaskTitle;
use App\Domain\UserAggregate\ValueObject\TodoId;
use App\Domain\UserAggregate\ValueObject\UserId;
use RuntimeException;

/**
 * ユースケース: ユーザーのTodoを更新する
 * 
 * アクター: ユーザー
 * 目的: タスクの内容を変更する
 * 
 * メインフロー:
 * 1. ユーザーが編集画面で新しいタスク名を入力
 * 2. システムがタスク名をバリデーション
 * 3. システムがUserを取得
 * 4. UserからTodoを検索
 * 5. Todoのタイトルを変更
 * 6. システムがUserを保存
 * 
 * 代替フロー:
 * - Userが見つからない → エラー
 * - Todoが見つからない → エラー
 * 
 * 事前条件:
 * - ユーザーが認証済み
 * - Todoがユーザーに属している
 * 
 * 事後条件:
 * - タスクのタイトルが更新されている
 */
class UpdateTodoOfUserService
{
    private UserRepositoryInterface $repository;

    public function __construct(UserRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * ユーザーのTodoを更新
     * 
     * @throws RuntimeException Userまたは Todoが見つからない場合
     */
    public function handle(int $userId, int $todoId, string $newTitle): void
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

        // Todoのタイトルを変更
        $todo->changeTitle(new TaskTitle($newTitle));

        // Userを保存
        $this->repository->save($user);
    }
}

