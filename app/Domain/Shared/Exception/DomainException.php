<?php

namespace App\Domain\Shared\Exception;

use RuntimeException;

/**
 * ドメイン層の例外基底クラス
 */
abstract class DomainException extends RuntimeException
{
    public function __construct(string $message = '', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
