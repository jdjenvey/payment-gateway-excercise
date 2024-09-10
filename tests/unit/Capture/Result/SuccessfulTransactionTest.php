<?php

declare(strict_types=1);

namespace VestiaireTest\Unit\Capture\Result;

use PHPUnit\Framework\TestCase;
use Vestiaire\Capture\Result\SuccessfulTransaction;
use Vestiaire\Payment\Storage\Transaction\TransactionInterface;
use PHPUnit\Framework\MockObject\MockObject;

class SuccessfulTransactionTest extends TestCase
{
    public function testIsValidReturnsTrue(): void
    {
        $transaction = $this->createTransactionMock();
        $result = new SuccessfulTransaction($transaction);

        $this->assertTrue($result->isValid());
    }

    public function testGetStatusCodeReturns200(): void
    {
        $transaction = $this->createTransactionMock();
        $result = new SuccessfulTransaction($transaction);

        $this->assertSame(200, $result->getStatusCode());
    }

    public function testJsonSerializeReturnsCorrectData(): void
    {
        $transaction = $this->createTransactionMock('tx12345');
        $result = new SuccessfulTransaction($transaction);

        $expected = [
            'status' => 'success',
            'transaction_id' => 'tx12345',
        ];

        $this->assertSame($expected, $result->jsonSerialize());
    }

    private function createTransactionMock(string $transactionId = 'default-tx'): TransactionInterface & MockObject
    {
        $mock = $this->createMock(TransactionInterface::class);

        $mock
            ->method('getTransactionId')
            ->willReturn($transactionId);

        return $mock;
    }
}
