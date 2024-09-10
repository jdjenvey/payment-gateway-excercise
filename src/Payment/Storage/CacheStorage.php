<?php

declare(strict_types=1);

namespace Vestiaire\Payment\Storage;

use Psr\Cache\CacheItemPoolInterface;
use Vestiaire\Payment\Storage\Hold\HoldInterface;
use Vestiaire\Payment\Storage\Refund\RefundInterface;
use Vestiaire\Payment\Storage\Transaction\TransactionInterface;

final readonly class CacheStorage implements StorageInterface
{
    public function __construct(
        private CacheItemPoolInterface $pool,
    ) {}

    public function findHold(string $cardNumber, string $amount, string $currency): ?HoldInterface
    {
        $key = $this->holdKey(
            $cardNumber,
            $amount,
            $currency
        );

        $entry = $this->pool->getItem($key);

        if ($entry->isHit()) {
            return $entry->get();
        }

        $this->pool->deleteItem($key);

        return null;
    }

    public function removeHold(HoldInterface $hold): void
    {
        $key = $this->holdKey(
            $hold->getCardNumber(),
            $hold->getAmount(),
            $hold->getCurrency()
        );

        $this->pool->deleteItem($key);
    }

    public function storeHold(HoldInterface $hold): void
    {
        $key = $this->holdKey(
            $hold->getCardNumber(),
            $hold->getAmount(),
            $hold->getCurrency()
        );

        $item = $this->pool->getItem($key);
        $item->set($hold);
        $item->expiresAfter($hold->getExpiryDatetime()->getTimestamp());

        $this->pool->save($item);
    }

    public function findTransaction(string $transactionId): ?TransactionInterface
    {
        $key = $this->transactionKey($transactionId);

        $entry = $this->pool->getItem($key);

        if (!$entry->isHit()) {
            return null;
        }

        return $entry->get();
    }

    public function storeTransaction(TransactionInterface $transaction): void
    {
        $key = $this->transactionKey($transaction->getTransactionId());

        $entry = $this->pool->getItem($key);
        $entry->set($transaction);

        $this->pool->save($entry);
    }

    public function findRefund(TransactionInterface $transaction): ?RefundInterface
    {
        $key = $this->refundKey($transaction->getTransactionId());

        $entry = $this->pool->getItem($key);

        if (!$entry->isHit()) {
            return null;
        }

        return $entry->get();
    }

    public function storeRefund(TransactionInterface $transaction, RefundInterface $refund): void
    {
        $key = $this->refundKey($transaction->getTransactionId());

        $entry = $this->pool->getItem($key);
        $entry->set($refund);

        $this->pool->save($entry);
    }

    private function holdKey(string $cardNumber, string $amount, string $currency): string
    {
        return $this->sanitiseKey(
            \sprintf(
                "%s|%s|%s|%s",
                HoldInterface::class,
                $cardNumber,
                $amount,
                $currency
            )
        );
    }

    private function transactionKey(string $transactionId): string
    {
        return $this->sanitiseKey(
            \sprintf(
                "%s|%s",
                TransactionInterface::class,
                $transactionId
            )
        );
    }

    private function refundKey(string $transactionId): string
    {
        return $this->sanitiseKey(
            \sprintf(
                "%s|%s",
                RefundInterface::class,
                $transactionId
            )
        );
    }

    private function sanitiseKey(string $key): string
    {
        return \str_replace(
            ['{', '}', '(', ')', '/', '\\', '@'],
            '',
            $key
        );
    }
}
