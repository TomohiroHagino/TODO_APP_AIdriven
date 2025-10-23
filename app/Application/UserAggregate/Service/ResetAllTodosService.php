<?php

namespace App\Application\UserAggregate\Service;

use App\Domain\UserAggregate\Repository\UserRepositoryInterface;

/**
 * ユースケース: 全ユーザーの全Todoを未完了に戻す
 * 
 * アクター: システム管理者（バッチ実行者）
 * 目的: すべてのTodoのステータスをリセットする
 * 
 * メインフロー:
 * 1. システムが全ユーザーを取得
 * 2. 各ユーザーの完了済みTodoを未完了に戻す
 * 3. 変更を保存
 * 
 * 代替フロー:
 * なし
 * 
 * 事前条件:
 * - なし
 * 
 * 事後条件:
 * - 全Todoのステータスが未完了になる
 */
class ResetAllTodosService
{
    private UserRepositoryInterface $repository;

    public function __construct(UserRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * 全Todoを未完了に戻す
     * 
     * @return array{totalUsers: int, totalTodos: int, resetCount: int} 処理結果
     */
    public function handle(): array
    {
        $users = $this->repository->findAll();
        
        $totalUsers = count($users);
        $totalTodos = 0;
        $resetCount = 0;

        foreach ($users as $user) {
            $todos = $user->getTodos();
            $totalTodos += count($todos);

            foreach ($todos as $todo) {
                if ($todo->isDone()) {
                    $todo->markAsPending();
                    $resetCount++;
                }
            }

            // 変更があった場合のみ保存
            if ($resetCount > 0) {
                $this->repository->save($user);
            }
        }

        return [
            'totalUsers' => $totalUsers,
            'totalTodos' => $totalTodos,
            'resetCount' => $resetCount,
        ];
    }
}

