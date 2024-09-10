<?php

declare(strict_types=1);

namespace VestiaireTest\Unit\Capture\Result;

use PHPUnit\Framework\TestCase;
use Vestiaire\Capture\Result\ResultFactory;
use Vestiaire\Capture\Result\SuccessfulTransaction;
use Vestiaire\Capture\Result\MissingHoldResult;
use Vestiaire\Capture\Result\InvalidTokenResult;
use Vestiaire\Capture\Validation\Exception\Exception;
use Vestiaire\Payment\Storage\Transaction\TransactionInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Vestiaire\Result\ResultInterface;

class ResultFactoryTest extends TestCase
{
    public function testCreateSuccessfulTransactionResult(): void
    {
        $transaction = $this->createTransactionMock();
        $factory = new ResultFactory();

        $result = $factory->createSuccessfulTransactionResult($transaction);

        $this->assertInstanceOf(SuccessfulTransaction::class, $result);
        $this->assertInstanceOf(ResultInterface::class, $result);
    }

    public function testCreateMissingHoldErrorResult(): void
    {
        $factory = new ResultFactory();

        $result = $factory->createMissingHoldErrorResult();

        $this->assertInstanceOf(MissingHoldResult::class, $result);
        $this->assertInstanceOf(ResultInterface::class, $result);
    }

    public function testCreateInvalidTokenErrorResult(): void
    {
        $exception = $this->createExceptionMock();
        $factory = new ResultFactory();

        $result = $factory->createInvalidTokenErrorResult($exception);

        $this->assertInstanceOf(InvalidTokenResult::class, $result);
        $this->assertInstanceOf(ResultInterface::class, $result);
    }

    private function createTransactionMock(): TransactionInterface & MockObject
    {
        return $this->createMock(TransactionInterface::class);
    }

    private function createExceptionMock(): Exception & MockObject
    {
        return $this->createMock(Exception::class);
    }
}
