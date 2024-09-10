<?php

declare(strict_types=1);

namespace Vestiaire\Payment\Storage;

use Brick\Money\Money;
use Vestiaire\Card\Card;
use Vestiaire\Payment\Storage\Hold\HoldInterface;
use Vestiaire\Payment\Storage\Refund\RefundInterface;
use Vestiaire\Payment\Storage\Transaction\TransactionInterface;

interface StorageInterface
{
    public function storeHold(HoldInterface $hold): void;

    public function removeHold(HoldInterface $hold): void;

    public function findHold(string $cardNumber, string $amount, string $currency): ?HoldInterface;

    public function storeTransaction(TransactionInterface $transaction): void;

    public function findTransaction(string $transactionId): ?TransactionInterface;

    public function storeRefund(TransactionInterface $transaction, RefundInterface $refund): void;

    public function findRefund(TransactionInterface $transaction): ?RefundInterface;
}