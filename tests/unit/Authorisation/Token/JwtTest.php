<?php

declare(strict_types=1);

namespace VestiaireTest\Unit\Authorisation\Token;

use PHPUnit\Framework\TestCase;
use Vestiaire\Authorisation\Token\Jwt;

class JwtTest extends TestCase
{
    public function testToStringReturnsToken(): void
    {
        $jwt = $this->createJwt(token: 'test-token');
        $this->assertSame('test-token', $jwt->toString());
    }

    public function testIsExpiredReturnsTrueWhenExpired(): void
    {
        $jwt = $this->createJwt(expiresAt: new \DateTimeImmutable('-1 day'));
        $this->assertTrue($jwt->isExpired());
    }

    public function testIsExpiredReturnsFalseWhenNotExpired(): void
    {
        $jwt = $this->createJwt(expiresAt: new \DateTimeImmutable('+1 day'));
        $this->assertFalse($jwt->isExpired());
    }

    public function testGetExpiryDateTimeReturnsCorrectDate(): void
    {
        $date = new \DateTimeImmutable('+1 day');
        $jwt = $this->createJwt(expiresAt: $date);
        $this->assertSame($date, $jwt->getExpiryDateTime());
    }

    public function testGetProviderIdentifierReturnsCorrectIdentifier(): void
    {
        $jwt = $this->createJwt(provider: 'provider-id');
        $this->assertSame('provider-id', $jwt->getProviderIdentifier());
    }

    public function testGetCardNumberReturnsCorrectNumber(): void
    {
        $jwt = $this->createJwt(card: '1234567890123456');
        $this->assertSame('1234567890123456', $jwt->getCardNumber());
    }

    public function testGetAmountReturnsCorrectAmount(): void
    {
        $jwt = $this->createJwt(amount: '100.00');
        $this->assertSame('100.00', $jwt->getAmount());
    }

    public function testGetCurrencyReturnsCorrectCurrency(): void
    {
        $jwt = $this->createJwt(currency: 'USD');
        $this->assertSame('USD', $jwt->getCurrency());
    }

    private function createJwt(
        string $token = 'token',
        ?\DateTimeInterface $expiresAt = null,
        string $provider = 'provider',
        string $card = '4111111111111111',
        string $amount = '123.45',
        string $currency = 'EUR',
    ): Jwt {
        $expiresAt = $expiresAt ?? new \DateTimeImmutable('+1 day');
        return new Jwt($token, $expiresAt, $provider, $card, $amount, $currency);
    }
}
