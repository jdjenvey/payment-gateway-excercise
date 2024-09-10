<?php

declare(strict_types=1);

namespace VestiaireTest\Unit\Payment\Storage\Hold;

use PHPUnit\Framework\TestCase;
use Vestiaire\Payment\Storage\Hold\Hold;

class HoldTest extends TestCase
{
    public function testGetEncodedTokenReturnsCorrectToken(): void
    {
        $hold = $this->createHold(token: 'token-123');
        $this->assertSame('token-123', $hold->getEncodedToken());
    }

    public function testGetAmountReturnsCorrectAmount(): void
    {
        $hold = $this->createHold(amount: '123.45');
        $this->assertSame('123.45', $hold->getAmount());
    }

    public function testGetCardNumberReturnsCorrectCardNumber(): void
    {
        $hold = $this->createHold(cardNumber: '4111111111111111');
        $this->assertSame('4111111111111111', $hold->getCardNumber());
    }

    public function testGetCurrencyReturnsCorrectCurrency(): void
    {
        $hold = $this->createHold(currency: 'USD');
        $this->assertSame('USD', $hold->getCurrency());
    }

    public function testIsExpiredReturnsFalseForFutureDate(): void
    {
        $hold = $this->createHold(expires: new \DateTimeImmutable('+1 day'));
        $this->assertFalse($hold->isExpired());
    }

    public function testIsExpiredReturnsTrueForPastDate(): void
    {
        $hold = $this->createHold(expires: new \DateTimeImmutable('-1 day'));
        $this->assertTrue($hold->isExpired());
    }

    public function testGetExpiryDatetimeReturnsCorrectExpiry(): void
    {
        $expiry = new \DateTimeImmutable('+1 day');
        $hold = $this->createHold(expires: $expiry);
        $this->assertSame($expiry, $hold->getExpiryDatetime());
    }

    private function createHold(
        string $cardNumber = '4111113451111111',
        string $amount = '560.34',
        string $currency = 'EUR',
        string $token = '398h99823h9h98fb23823f98bius98gfw98g3wf8n',
        \DateTimeInterface $expires = new \DateTimeImmutable('+1 day')
    ): Hold {
        return new Hold(
            cardNumber: $cardNumber,
            amount: $amount,
            currency: $currency,
            token: $token,
            expires: $expires
        );
    }
}
