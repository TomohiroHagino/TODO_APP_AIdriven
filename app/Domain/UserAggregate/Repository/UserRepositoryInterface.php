<?php

namespace App\Domain\UserAggregate\Repository;

use App\Domain\UserAggregate\Entity\UserEntity;
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
     * UserEntityを保存（作成または更新）
     * 
     * UserEntityに紐づくTodosも一緒に保存
     */
    public function save(UserEntity $user): void;

    /**
     * UserIDでUserEntityを検索
     * 
     * Todosも一緒に取得
     */
    public function findById(UserId $userId): ?UserEntity;

    /**
     * EmailでUserEntityを検索
     * 
     * Todosも一緒に取得
     */
    public function findByEmail(Email $email): ?UserEntity;

    /**
     * 全UserEntityを取得
     * 
     * @return UserEntity[]
     */
    public function findAll(): array;

    /**
     * UserEntityを削除
     * 
     * カスケードでTodosも削除される
     */
    public function delete(UserId $userId): void;

    /**
     * EmailがすでにUSE されているか確認
     */
    public function existsByEmail(Email $email): bool;
}

