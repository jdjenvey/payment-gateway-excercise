<?php

declare(strict_types=1);

namespace Vestiaire\Payment\Storage\Refund\Exception;

final class InvalidRefundIdException extends \InvalidArgumentException implements Exception
{
    public function __construct(string $transactionId)
    {
        parent::__construct(
            \sprintf(
                "Refund ID '%s' is not valid. ID must be in the format 'rf123456789'",
                $transactionId
            )
        );
    }
}
