<?php

declare(strict_types=1);

namespace Vestiaire\Refund;

use Brick\Money\Money;
use Vestiaire\Payment\Provider\ProviderInterface;
use Vestiaire\Payment\Storage\Refund\PrefixedRefund;
use Vestiaire\Payment\Storage\StorageInterface;
use Vestiaire\Payment\Storage\Transaction\TransactionInterface;
use Vestiaire\Result\ResultInterface;

final class Refunder implements RefundInterface
{
    public function __construct(
        private Result\FactoryInterface $factory,
        private StorageInterface $storage,
    ) {}

    public function refund(ProviderInterface $provider, TransactionInterface $transaction, Money $amount): ResultInterface
    {
        $stored = $this->storage->findTransaction(
            $transaction->getTransactionId()
        );

        if (null === $stored) {
            return $this->factory->createMissingTransactionResult($transaction);
        }

        $refund = $this->storage->findRefund($transaction);

        if (null !== $refund) {
            return $this->factory->createExistingRefundResult($refund);
        }

        $refund = PrefixedRefund::generate();

        $this->storage->storeRefund($transaction, $refund);

        return $this->factory->createSuccessfulRefundResult($refund);
    }
}
