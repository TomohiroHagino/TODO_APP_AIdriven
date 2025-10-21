<?php

namespace App\Domain\UserAggregate\Repository;

use App\Domain\UserAggregate\Entity\User;
use App\Domain\UserAggregate\ValueObject\Email;
use App\Domain\UserAggregate\ValueObject\UserId;

/**
 * Userリポジトリインターフェース
 * 
 * UserAggregateの永続化を担当
 */
interface UserRepositoryInterface
{
    /**
     * 次のUserIDを生成
     */
    public function nextUserId(): UserId;

    /**
     * 次のTodoIDを生成
     */
    public function nextTodoId(): int;

    /**
     * Userを保存（作成または更新）
     * 
     * Userに紐づくTodosも一緒に保存
     */
    public function save(User $user): void;

    /**
     * UserIDでUserを検索
     * 
     * Todosも一緒に取得
     */
    public function findById(UserId $userId): ?User;

    /**
     * EmailでUserを検索
     * 
     * Todosも一緒に取得
     */
    public function findByEmail(Email $email): ?User;

    /**
     * 全Userを取得
     * 
     * @return User[]
     */
    public function findAll(): array;

    /**
     * Userを削除
     * 
     * カスケードでTodosも削除される
     */
    public function delete(UserId $userId): void;

    /**
     * EmailがすでにUSE されているか確認
     */
    public function existsByEmail(Email $email): bool;
}

