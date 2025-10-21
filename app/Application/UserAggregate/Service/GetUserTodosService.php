<?php

namespace App\Application\UserAggregate\Service;

use App\Domain\UserAggregate\Entity\TodoEntity;
use App\Domain\UserAggregate\Repository\UserRepositoryInterface;
use App\Domain\UserAggregate\ValueObject\UserId;
use RuntimeException;

/**
 * ユースケース: ユーザーのTodo一覧を取得する
 * 
 * アクター: ユーザー
 * 目的: 登録されているタスクの一覧を確認する
 * 
 * メインフロー:
 * 1. ユーザーが一覧ページにアクセス
 * 2. システムがUserを取得
 * 3. システムがUserのTodosを取得
 * 4. システムが一覧画面を表示
 * 
 * 代替フロー:
 * - フィルター「完了」選択 → 完了Todosのみ取得
 * - フィルター「未完了」選択 → 未完了Todosのみ取得
 * 
 * 事前条件:
 * - ユーザーが認証済み
 * 
 * 事後条件:
 * - なし（参照のみ）
 */
class GetUserTodosService
{
    private UserRepositoryInterface $repository;

    public function __construct(UserRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * ユーザーの全Todosを取得
     * 
     * @return Todo[]
     * @throws RuntimeException Userが見つからない場合
     */
    public function handle(int $userId): array
    {
        $user = $this->repository->findById(new UserId($userId));
        
        if (!$user) {
            throw new RuntimeException("ユーザーが見つかりません（ID: {$userId}）");
        }

        return $user->getTodos();
    }

    /**
     * ステータスでフィルタリングしたTodosを取得
     * 
     * @return Todo[]
     * @throws RuntimeException Userが見つからない場合
     */
    public function handleByStatus(int $userId, bool $isDone): array
    {
        $user = $this->repository->findById(new UserId($userId));
        
        if (!$user) {
            throw new RuntimeException("ユーザーが見つかりません（ID: {$userId}）");
        }

        return $isDone ? $user->getDoneTodos() : $user->getPendingTodos();
    }
}

