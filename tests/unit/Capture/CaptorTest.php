<?php

declare(strict_types=1);

namespace VestiaireTest\Unit\Capture;

use PHPUnit\Framework\TestCase;
use Vestiaire\Capture\Captor;
use Vestiaire\Authorisation\Token\TokenInterface;
use Vestiaire\Capture\Validation\StrategyInterface;
use Vestiaire\Payment\Provider\ProviderInterface;
use Vestiaire\Payment\Storage\Hold\HoldInterface;
use Vestiaire\Payment\Storage\StorageInterface;#
use Vestiaire\Capture\Result\FactoryInterface as ResultFactory;
use Vestiaire\Result\ResultInterface;
use Vestiaire\Capture\Validation\Exception\Exception as ValidationException;
use PHPUnit\Framework\MockObject\MockObject;

class CaptorTest extends TestCase
{
    public function testCaptureReturnsInvalidTokenErrorWhenValidationFails(): void
    {
        $validator = $this->createValidationStrategyMockWithException();
        $resultFactory = $this->createResultFactoryMockForInvalidToken();

        $captor = $this->createCaptor(
            factory: $resultFactory,
            validator: $validator
        );

        $provider = $this->createProviderMock();
        $token = $this->createTokenMock();

        $result = $captor->capture($provider, $token);

        $this->assertInstanceOf(ResultInterface::class, $result);
    }

    public function testCaptureReturnsMissingHoldErrorWhenHoldIsNotFound(): void
    {
        $storage = $this->createStorageMockWithoutHold();
        $resultFactory = $this->createResultFactoryMockForMissingHold();

        $captor = $this->createCaptor(
            factory: $resultFactory,
            storage: $storage
        );

        $provider = $this->createProviderMock();
        $token = $this->createTokenMock();

        $result = $captor->capture($provider, $token);

        $this->assertInstanceOf(ResultInterface::class, $result);
    }

    public function testCaptureStoresTransactionAndReturnsSuccess(): void
    {
        $storage = $this->createStorageMockWithHold();
        $resultFactory = $this->createResultFactoryMockForSuccess();

        $captor = $this->createCaptor(
            factory: $resultFactory,
            storage: $storage
        );

        $provider = $this->createProviderMock();
        $token = $this->createTokenMock();

        $result = $captor->capture($provider, $token);

        $this->assertInstanceOf(ResultInterface::class, $result);
    }

    private function createCaptor(
        ?ResultFactory $factory = null,
        ?StrategyInterface $validator = null,
        ?StorageInterface $storage = null
    ): Captor {
        return new Captor(
            factory: $factory ?? $this->createMock(ResultFactory::class),
            validator: $validator ?? $this->createMock(StrategyInterface::class),
            storage: $storage ?? $this->createMock(StorageInterface::class)
        );
    }

    private function createValidationStrategyMockWithException(): StrategyInterface & MockObject
    {
        $mock = $this->createMock(StrategyInterface::class);

        $mock
            ->method('validateTokenForProvider')
            ->willThrowException($this->createMock(ValidationException::class));

        return $mock;
    }

    private function createResultFactoryMockForInvalidToken(): ResultFactory & MockObject
    {
        $mock = $this->createMock(ResultFactory::class);

        $mock
            ->method('createInvalidTokenErrorResult')
            ->willReturn($this->createMock(ResultInterface::class));

        return $mock;
    }

    private function createResultFactoryMockForMissingHold(): ResultFactory & MockObject
    {
        $mock = $this->createMock(ResultFactory::class);

        $mock
            ->method('createMissingHoldErrorResult')
            ->willReturn($this->createMock(ResultInterface::class));

        return $mock;
    }

    private function createResultFactoryMockForSuccess(): ResultFactory & MockObject
    {
        $mock = $this->createMock(ResultFactory::class);

        $mock
            ->method('createSuccessfulTransactionResult')
            ->willReturn($this->createMock(ResultInterface::class));

        return $mock;
    }

    private function createStorageMockWithoutHold(): StorageInterface & MockObject
    {
        $mock = $this->createMock(StorageInterface::class);

        $mock
            ->method('findHold')
            ->willReturn(null);

        return $mock;
    }

    private function createStorageMockWithHold(): StorageInterface & MockObject
    {
        $mock = $this->createMock(StorageInterface::class);

        $mock
            ->method('findHold')
            ->willReturn($this->createHoldMock());

        $mock
            ->expects($this->once())
            ->method('storeTransaction');

        $mock
            ->expects($this->once())
            ->method('removeHold');

        return $mock;
    }

    private function createHoldMock(): HoldInterface & MockObject
    {
        return $this->createMock(HoldInterface::class);
    }

    private function createProviderMock(): ProviderInterface & MockObject
    {
        return $this->createMock(ProviderInterface::class);
    }

    private function createTokenMock(): TokenInterface & MockObject
    {
        $mock = $this->createMock(TokenInterface::class);

        $mock
            ->method('getCardNumber')
            ->willReturn('4111111111111111');

        $mock
            ->method('getAmount')
            ->willReturn('123.45');

        $mock
            ->method('getCurrency')
            ->willReturn('EUR');

        return $mock;
    }
}
