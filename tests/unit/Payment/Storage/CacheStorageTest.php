<?php

declare(strict_types=1);

namespace VestiaireTest\Unit\Payment\Storage;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Vestiaire\Payment\Storage\CacheStorage;
use Vestiaire\Payment\Storage\Hold\HoldInterface;
use Vestiaire\Payment\Storage\Transaction\TransactionInterface;
use Vestiaire\Payment\Storage\Refund\RefundInterface;

class CacheStorageTest extends TestCase
{
    public function testFindHoldReturnsHoldIfCached(): void
    {
        $cachePool = new ArrayAdapter();
        $hold = $this->createHoldMock();

        $cardNumber = '4111111111111111';
        $amount = '123.45';
        $currency = 'USD';

        $item = $cachePool->getItem(
            $this->santiseKey(
                HoldInterface::class,
                $cardNumber,
                $amount,
                $currency
            )
        );

        $item->set($hold);

        $cachePool->save($item);

        $storage = new CacheStorage($cachePool);

        $foundHold = $storage->findHold(
            $cardNumber,
            $amount,
            $currency
        );

        $this->assertNotNull($foundHold);
        $this->assertSame($hold->getCardNumber(), $foundHold->getCardNumber());
        $this->assertSame($hold->getAmount(), $foundHold->getAmount());
        $this->assertSame($hold->getCurrency(), $foundHold->getCurrency());
        $this->assertSame($hold->getEncodedToken(), $foundHold->getEncodedToken());
    }

    public function testFindHoldReturnsNullIfNotCached(): void
    {
        $cachePool = new ArrayAdapter();
        $storage = new CacheStorage($cachePool);

        $foundHold = $storage->findHold('4111111111111111', '123.45', 'USD');

        $this->assertNull($foundHold);
    }

    public function testRemoveHoldDeletesHoldFromCache(): void
    {
        $cachePool = new ArrayAdapter();
        $hold = $this->createHoldMock();
        $storage = new CacheStorage($cachePool);

        $cardNumber = '4111111111111111';
        $amount = '123.45';
        $currency = 'USD';

        // Store the hold in the cache
        $storage->storeHold($hold);

        // Remove the hold
        $storage->removeHold($hold);

        $cachedItem = $cachePool
            ->getItem(
                $this->santiseKey(
                    HoldInterface::class,
                    $cardNumber,
                    $amount,
                    $currency
                )
            );

        $this->assertFalse($cachedItem->isHit());
    }

    public function testFindTransactionReturnsTransactionIfCached(): void
    {
        $cachePool = new ArrayAdapter();
        $transaction = $this->createTransactionMock();

        $id = 'tx123456789';

        // Store the transaction in the cache
        $item = $cachePool->getItem(
            $this->santiseKey(
                TransactionInterface::class,
                $id
            )
        );

        $item->set($transaction);

        $cachePool->save($item);

        $storage = new CacheStorage($cachePool);

        $foundTransaction = $storage->findTransaction($id);

        $this->assertSame($transaction->getTransactionId(), $foundTransaction->getTransactionId());
    }

    public function testCanStoreAndReturnTransaction(): void
    {
        $cachePool = new ArrayAdapter();
        $transaction = $this->createTransactionMock();

        $storage = new CacheStorage($cachePool);

        $storage->storeTransaction($transaction);

        $foundTransaction = $storage->findTransaction('tx123456789');

        $this->assertSame($transaction->getTransactionId(), $foundTransaction->getTransactionId());
    }

    public function testFindRefundReturnsRefundIfCached(): void
    {
        $cachePool = new ArrayAdapter();
        $refund = $this->createRefundMock();
        $transaction = $this->createTransactionMock();

        $item = $cachePool->getItem(
            $this->santiseKey(
                RefundInterface::class,
                'tx123456789'
            )
        );

        $item->set($refund);

        $cachePool->save($item);

        $storage = new CacheStorage($cachePool);

        $foundRefund = $storage->findRefund($transaction);

        $this->assertNotNull($foundRefund);
        $this->assertSame($refund->getRefundId(), $foundRefund->getRefundId());
    }

    public function testCannotFindRefundIfNotCached(): void
    {
        $cachePool = new ArrayAdapter();
        $transaction = $this->createTransactionMock('tx999999999');

        $storage = new CacheStorage($cachePool);

        $this->assertNull($storage->findRefund($transaction));
    }

    public function testCannotFindTransactionIfNotCached(): void
    {
        $cachePool = new ArrayAdapter();
        $storage = new CacheStorage($cachePool);

        $this->assertNull($storage->findTransaction('tx999999999'));
    }

    public function testStoreRefundSavesRefundInCache(): void
    {
        $cachePool = new ArrayAdapter();
        $refund = $this->createRefundMock();
        $transaction = $this->createTransactionMock();
        $storage = new CacheStorage($cachePool);

        $storage->storeRefund($transaction, $refund);

        $cachedItem = $cachePool
            ->getItem(
                $this->santiseKey('Vestiaire\Payment\Storage\Refund\RefundInterface|tx123456789')
            );

        $this->assertTrue($cachedItem->isHit());
        $this->assertSame($refund->getRefundId(), $cachedItem->get()->getRefundId());
    }

    private function createHoldMock(): HoldInterface
    {
        $mock = $this->createMock(HoldInterface::class);
        $mock->method('getEncodedToken')->willReturn('2r98h2f39gh298fh298fh928h392g');
        $mock->method('getCardNumber')->willReturn('4111111111111111');
        $mock->method('getAmount')->willReturn('123.45');
        $mock->method('getCurrency')->willReturn('USD');
        $mock->method('getExpiryDatetime')->willReturn(new \DateTimeImmutable('+1 hour'));

        return $mock;
    }

    private function createTransactionMock(string $id = 'tx123456789'): TransactionInterface
    {
        $mock = $this->createMock(TransactionInterface::class);

        $mock
            ->method('getTransactionId')
            ->willReturn($id);

        return $mock;
    }

    private function createRefundMock(): RefundInterface
    {
        $refund = $this->createMock(RefundInterface::class);

        $refund
            ->method('getRefundId')
            ->willReturn('rf123456789');

        return $refund;
    }

    private function santiseKey(string ...$key): string
    {
        return str_replace(
            ['{', '}', '(', ')', '/', '\\', '@'],
            '',
            \implode('|', $key)
        );
    }
}
