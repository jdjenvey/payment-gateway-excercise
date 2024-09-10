<?php

declare(strict_types=1);

namespace VestiaireTest\Unit\Capture\Validation;

use PHPUnit\Framework\TestCase;
use Vestiaire\Capture\Validation\TokenNotExpiredStrategy;
use Vestiaire\Authorisation\Token\TokenInterface;
use Vestiaire\Capture\Validation\Exception\TokenExpiredException;
use Vestiaire\Payment\Provider\ProviderInterface;
use PHPUnit\Framework\MockObject\MockObject;

class TokenNotExpiredStrategyTest extends TestCase
{
    public function testValidateTokenForProviderDoesNotThrowWhenTokenIsNotExpired(): void
    {
        $token = $this->createTokenMock(false); // Token is not expired
        $provider = $this->createProviderMock();

        $strategy = new TokenNotExpiredStrategy();

        $strategy->validateTokenForProvider($token, $provider);

        $this->assertTrue(true); // If no exception is thrown, the test passes
    }

    public function testValidateTokenForProviderThrowsExceptionWhenTokenIsExpired(): void
    {
        $token = $this->createTokenMock(true); // Token is expired
        $provider = $this->createProviderMock();

        $strategy = new TokenNotExpiredStrategy();

        $this->expectException(TokenExpiredException::class);

        $strategy->validateTokenForProvider($token, $provider);
    }

    private function createTokenMock(bool $isExpired): TokenInterface & MockObject
    {
        $mock = $this->createMock(TokenInterface::class);

        $mock
            ->method('isExpired')
            ->willReturn($isExpired);

        return $mock;
    }

    private function createProviderMock(): ProviderInterface & MockObject
    {
        return $this->createMock(ProviderInterface::class);
    }
}
