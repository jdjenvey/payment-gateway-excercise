<?php

declare(strict_types=1);

namespace Vestiaire\Payment\Storage\Transaction\Exception;

final class InvalidTransactionIdException extends \InvalidArgumentException implements Exception
{
    public function __construct(string $transactionId)
    {
        parent::__construct(
            \sprintf(
                "Transaction ID '%s' is not valid. ID must be in the format 'tx123456789'",
                $transactionId
            )
        );
    }
}
