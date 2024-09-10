<?php

declare(strict_types=1);

namespace Vestiaire\Capture\Result;

use Vestiaire\Capture\Validation\Exception\Exception;
use Vestiaire\Payment\Storage\Transaction\TransactionInterface;
use Vestiaire\Result\ResultInterface;

interface FactoryInterface
{
    public function createSuccessfulTransactionResult(TransactionInterface $transaction): ResultInterface;

    public function createMissingHoldErrorResult(): ResultInterface;

    public function createInvalidTokenErrorResult(Exception $exception): ResultInterface;
}
