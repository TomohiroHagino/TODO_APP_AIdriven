<?php

namespace App\Domain\UserAggregate\Service;

use App\Domain\Shared\Exception\DomainException;
use App\Domain\UserAggregate\Repository\UserRepositoryInterface;
use App\Domain\UserAggregate\ValueObject\Email;

/**
 * Userドメインサービス
 * 
 * エンティティに収まらないドメインロジックを実装
 */
class UserDomainService
{
    private UserRepositoryInterface $repository;

    public function __construct(UserRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * メールアドレスが重複していないかチェック
     * 
     * @throws DuplicateEmailException
     */
    public function checkEmailUniqueness(Email $email): void
    {
        if ($this->repository->existsByEmail($email)) {
            throw new DuplicateEmailException(
                "メールアドレス「{$email->getValue()}」は既に使用されています"
            );
        }
    }
}

/**
 * メールアドレス重複例外
 */
class DuplicateEmailException extends DomainException
{
}

