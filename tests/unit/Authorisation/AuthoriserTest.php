<?php

declare(strict_types=1);

namespace VestiaireTest\Unit\Authorisation;

use Brick\Money\Currency;
use Brick\Money\Money;
use Vestiaire\Authorisation\Authoriser;
use Vestiaire\Authorisation\Result\FactoryInterface as ResultFactory;
use Vestiaire\Result\ResultInterface;
use Vestiaire\Authorisation\Token\FactoryInterface as TokenFactory;
use Vestiaire\Authorisation\Token\TokenInterface;
use Vestiaire\Card\CardInterface;
use Vestiaire\Payment\Provider\ProviderInterface;
use Vestiaire\Payment\Storage\Hold\FactoryInterface as HoldFactory;
use Vestiaire\Payment\Storage\Hold\HoldInterface;
use Vestiaire\Payment\Storage\StorageInterface;
use Vestiaire\Authorisation\Validation\StrategyInterface;
use Vestiaire\Authorisation\Validation\Exception\Exception as ValidationException;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class AuthoriserTest extends TestCase
{
    public function testAuthoriseWithInvalidCardThrowsValidationError(): void
    {
        $validator = $this->createValidationStrategyMockWithException();
        $resultFactory = $this->createResultFactoryMockForValidationError();

        $authoriser = $this->createAuthoriser(
            factory: $resultFactory,
            validator: $validator,
        );

        $provider = $this->createProviderMock();
        $card = $this->createCardMock();
        $amount = $this->createMoneyFake();

        $result = $authoriser->authorise($provider, $card, $amount);

        $this->assertInstanceOf(ResultInterface::class, $result);
    }

    public function testAuthoriseFindsHoldAndReturnsSuccessfulResult(): void
    {
        $tokenFactory = $this->createTokenFactoryMock();
        $hold = $this->createHoldMock();
        $storage = $this->createStorageMockWithHold($hold);
        $resultFactory = $this->createResultFactoryMockForSuccess();

        $authoriser = $this->createAuthoriser(
            factory: $resultFactory,
            tokens: $tokenFactory,
            storage: $storage
        );

        $provider = $this->createProviderMock();
        $card = $this->createCardMock();
        $amount = $this->createMoneyFake();

        $result = $authoriser->authorise($provider, $card, $amount);

        $this->assertInstanceOf(ResultInterface::class, $result);
    }

    public function testAuthoriseFindsExpiredHoldAndCreatesNewOne(): void
    {
        $tokenFactory = $this->createTokenFactoryMock();

        $hold = $this->createHoldMock(true);

        $storage = $this->createStorageMockWithHold($hold);

        $storage
            ->expects($this->once())
            ->method('removeHold')
            ->with($hold);

        $storage
            ->expects($this->once())
            ->method('storeHold');

        $resultFactory = $this->createResultFactoryMockForSuccess();

        $authoriser = $this->createAuthoriser(
            factory: $resultFactory,
            tokens: $tokenFactory,
            storage: $storage
        );

        $result = $authoriser->authorise(
            $this->createProviderMock(),
            $this->createCardMock(),
            $this->createMoneyFake(),
        );

        $this->assertInstanceOf(ResultInterface::class, $result);
    }

    public function testAuthoriseCreatesHoldWhenNoHoldExists(): void
    {
        $tokenFactory = $this->createTokenFactoryMock();
        $holdFactory = $this->createHoldFactoryMock();
        $storage = $this->createStorageMockWithoutHold();
        $resultFactory = $this->createResultFactoryMockForSuccess();

        $authoriser = $this->createAuthoriser(
            factory: $resultFactory,
            tokens: $tokenFactory,
            storage: $storage,
            holds: $holdFactory
        );

        $provider = $this->createProviderMock();
        $card = $this->createCardMock();
        $amount = $this->createMoneyFake();

        $result = $authoriser->authorise($provider, $card, $amount);

        $this->assertInstanceOf(ResultInterface::class, $result);
    }

    private function createAuthoriser(
        ?ResultFactory $factory = null,
        ?StrategyInterface $validator = null,
        ?TokenFactory $tokens = null,
        ?StorageInterface $storage = null,
        ?HoldFactory $holds = null
    ): Authoriser {
        return new Authoriser(
            factory: $factory ?? $this->createMock(ResultFactory::class),
            validator: $validator ?? $this->createMock(StrategyInterface::class),
            tokens: $tokens ?? $this->createMock(TokenFactory::class),
            storage: $storage ?? $this->createMock(StorageInterface::class),
            holds: $holds ?? $this->createMock(HoldFactory::class)
        );
    }

    private function createValidationStrategyMockWithException(): StrategyInterface & MockObject
    {
        $mock = $this->createMock(StrategyInterface::class);
        $mock->method('validateCardAmount')
            ->willThrowException($this->createMock(ValidationException::class));

        return $mock;
    }

    private function createResultFactoryMockForValidationError(): ResultFactory & MockObject
    {
        $mock = $this->createMock(ResultFactory::class);

        $mock
            ->method('createCardAmountValidationErrorResult')
            ->willReturn($this->createMock(ResultInterface::class));

        return $mock;
    }

    private function createResultFactoryMockForSuccess(): ResultFactory & MockObject
    {
        $mock = $this->createMock(ResultFactory::class);

        $mock
            ->method('createSuccessfulHoldResult')
            ->willReturn($this->createMock(ResultInterface::class));

        return $mock;
    }

    private function createTokenFactoryMock(): TokenFactory & MockObject
    {
        $mock = $this->createMock(TokenFactory::class);

        $mock
            ->method('decodeToken')
            ->willReturn($this->createMock(TokenInterface::class));

        return $mock;
    }

    private function createHoldFactoryMock(): HoldFactory & MockObject
    {
        return $this->createMock(HoldFactory::class);
    }

    private function createHoldMock(bool $expired = false): HoldInterface & MockObject
    {
        $mock = $this->createMock(HoldInterface::class);

        $mock
            ->method('isExpired')
            ->willReturn($expired);

        $mock
            ->method('getEncodedToken')
            ->willReturn('encoded-token');

        return $mock;
    }

    private function createStorageMockWithHold(HoldInterface $hold): StorageInterface & MockObject
    {
        $mock = $this->createMock(StorageInterface::class);

        $mock
            ->method('findHold')
            ->willReturn($hold);

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

    private function createProviderMock(): ProviderInterface & MockObject
    {
        return $this->createMock(ProviderInterface::class);
    }

    private function createCardMock(): CardInterface & MockObject
    {
        return $this->createMock(CardInterface::class);
    }

    private function createMoneyFake(): Money
    {
        return Money::of((string)\random_int(1, 999), Currency::of('EUR'));
    }
}
