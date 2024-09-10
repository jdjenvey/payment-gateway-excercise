<?php

declare(strict_types=1);

namespace Vestiaire\Refund;

use Brick\Money\Money;
use Vestiaire\Payment\Provider\ProviderInterface;
use Vestiaire\Payment\Storage\Transaction\TransactionInterface;
use Vestiaire\Result\ResultInterface;

interface RefundInterface
{
    public function refund(ProviderInterface $provider, TransactionInterface $transaction, Money $amount): ResultInterface;
}