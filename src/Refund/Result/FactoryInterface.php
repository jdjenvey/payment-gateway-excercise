<?php

declare(strict_types=1);

namespace Vestiaire\Refund\Result;

use Vestiaire\Payment\Storage\Transaction\TransactionInterface;
use Vestiaire\Payment\Storage\Refund\RefundInterface;
use Vestiaire\Result\ResultInterface;

interface FactoryInterface
{
    public function createMissingTransactionResult(TransactionInterface $transaction): ResultInterface;

    public function createExistingRefundResult(RefundInterface $refund): ResultInterface;

    public function createSuccessfulRefundResult(RefundInterface $refund): ResultInterface;
}