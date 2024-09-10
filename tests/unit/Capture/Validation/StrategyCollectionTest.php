<?php

declare(strict_types=1);

namespace VestiaireTest\Unit\Capture\Validation;

use PHPUnit\Framework\TestCase;
use Vestiaire\Capture\Validation\StrategyCollection;
use Vestiaire\Capture\Validation\StrategyInterface;
use Vestiaire\Authorisation\Token\TokenInterface;
use Vestiaire\Payment\Provider\ProviderInterface;
use PHPUnit\Framework\MockObject\MockObject;

class StrategyCollectionTest extends TestCase
{
    public function testCanHaveNoStrategies(): void
    {
        $strategyCollection = new StrategyCollection();

        $strategyCollection->validateTokenForProvider(
            $this->createTokenMock(),
            $this->createProviderMock()
        );

        $this->assertTrue(true);
    }

    public function testCanHaveOneStrategy(): void
    {
        $strategy1 = $this->createStrategyMock();

        $token = $this->createTokenMock();
        $provider = $this->createProviderMock();

        $strategy1
            ->expects($this->once())
            ->method('validateTokenForProvider')
            ->with($token, $provider);

        $strategyCollection = new StrategyCollection(
            $strategy1
        );

        $strategyCollection->validateTokenForProvider(
            $token,
            $provider
        );

        $this->assertTrue(true);
    }

    public function testCanHaveMultiplesStrategies(): void
    {
        $strategy1 = $this->createStrategyMock();
        $strategy2 = $this->createStrategyMock();
        $strategy3 = $this->createStrategyMock();

        $strategyCollection = new StrategyCollection(
            $strategy1,
            $strategy2,
            $strategy3
        );

        $token = $this->createTokenMock();
        $provider = $this->createProviderMock();

        $strategy1
            ->expects($this->once())
            ->method('validateTokenForProvider')
            ->with($token, $provider);

        $strategy2
            ->expects($this->once())
            ->method('validateTokenForProvider')
            ->with($token, $provider);

        $strategy3
            ->expects($this->once())
            ->method('validateTokenForProvider')
            ->with($token, $provider);

        $strategyCollection->validateTokenForProvider($token, $provider);
    }

    private function createStrategyMock(): StrategyInterface & MockObject
    {
        return $this->createMock(StrategyInterface::class);
    }

    private function createTokenMock(): TokenInterface & MockObject
    {
        return $this->createMock(TokenInterface::class);
    }

    private function createProviderMock(): ProviderInterface & MockObject
    {
        return $this->createMock(ProviderInterface::class);
    }
}
