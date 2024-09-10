<?php

declare(strict_types=1);

namespace Vestiaire\Payment\Storage\Transaction;

interface TransactionInterface
{
    public function getTransactionId(): string;
}