<?php

declare(strict_types=1);

namespace Vestiaire\Capture\Result;

use Vestiaire\Capture\Validation\Exception\Exception;
use Vestiaire\Payment\Storage\Transaction\TransactionInterface;
use Vestiaire\Result\ResultInterface;

final class ResultFactory implements FactoryInterface
{
    public function createSuccessfulTransactionResult(TransactionInterface $transaction): ResultInterface
    {
        return new SuccessfulTransaction($transaction);
    }

    public function createMissingHoldErrorResult(): ResultInterface
    {
        return new MissingHoldResult();
    }

    public function createInvalidTokenErrorResult(Exception $exception): ResultInterface
    {
        return new InvalidTokenResult($exception);
    }
}
