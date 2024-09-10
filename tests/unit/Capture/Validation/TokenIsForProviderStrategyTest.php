<?php

declare(strict_types=1);

namespace VestiaireTest\Unit\Capture\Validation;

use PHPUnit\Framework\TestCase;
use Vestiaire\Capture\Validation\TokenIsForProviderStrategy;
use Vestiaire\Authorisation\Token\TokenInterface;
use Vestiaire\Capture\Validation\Exception\ProviderMismatchException;
use Vestiaire\Payment\Provider\ProviderInterface;
use PHPUnit\Framework\MockObject\MockObject;

class TokenIsForProviderStrategyTest extends TestCase
{
    public function testValidateTokenForProviderDoesNotThrowWhenIdentifiersMatch(): void
    {
        $token = $this->createTokenMock('provider-id');
        $provider = $this->createProviderMock('provider-id');

        $strategy = new TokenIsForProviderStrategy();

        $strategy->validateTokenForProvider($token, $provider);

        $this->assertTrue(true); // If no exception is thrown, the test passes
    }

    public function testValidateTokenForProviderThrowsExceptionWhenIdentifiersDoNotMatch(): void
    {
        $token = $this->createTokenMock('provider-id');
        $provider = $this->createProviderMock('different-provider-id');

        $strategy = new TokenIsForProviderStrategy();

        $this->expectException(ProviderMismatchException::class);

        $strategy->validateTokenForProvider($token, $provider);
    }

    private function createTokenMock(string $providerIdentifier): TokenInterface & MockObject
    {
        $mock = $this->createMock(TokenInterface::class);

        $mock
            ->method('getProviderIdentifier')
            ->willReturn($providerIdentifier);

        return $mock;
    }

    private function createProviderMock(string $identifier): ProviderInterface & MockObject
    {
        $mock = $this->createMock(ProviderInterface::class);

        $mock
            ->method('getIdentifier')
            ->willReturn($identifier);

        return $mock;
    }
}
