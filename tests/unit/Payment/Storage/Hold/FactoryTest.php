<?php

declare(strict_types=1);

namespace VestiaireTest\Unit\Payment\Storage\Hold;

use Brick\Money\Currency;
use PHPUnit\Framework\TestCase;
use Vestiaire\Card\Number\CardNumberInterface;
use Vestiaire\Payment\Storage\Hold\Factory;
use Vestiaire\Card\CardInterface;
use Brick\Money\Money;
use Vestiaire\Authorisation\Token\TokenInterface;
use Vestiaire\Payment\Storage\Hold\HoldInterface;
use PHPUnit\Framework\MockObject\MockObject;

class FactoryTest extends TestCase
{
    public function testForCardAmountReturnsHoldInterface(): void
    {
        $date = new \DateTimeImmutable('+1 day');
        $card = $this->createCardMock('4111111111111111');
        $amount = $this->createMoneyFake('123.45', 'USD');
        $token = $this->createTokenMock('token-123', $date);

        $factory = new Factory();

        $hold = $factory->forCardAmount($card, $amount, $token);

        $this->assertInstanceOf(HoldInterface::class, $hold);
        $this->assertSame('4111111111111111', $hold->getCardNumber());
        $this->assertSame('123.45', $hold->getAmount());
        $this->assertSame('USD', $hold->getCurrency());
        $this->assertSame('token-123', $hold->getEncodedToken());
        $this->assertEquals($date, $hold->getExpiryDateTime());
    }

    private function createCardMock(string $number): CardInterface & MockObject
    {
        $mock = $this->createMock(CardInterface::class);
        $cardNumber = $this->createMock(CardNumberInterface::class);

        $cardNumber
            ->method('__toString')
            ->willReturn($number);

        $mock
            ->method('cardNumber')
            ->willReturn($cardNumber);

        return $mock;
    }

    private function createMoneyFake(string $amount, string $currency): Money
    {
        return Money::of($amount, Currency::of($currency));
    }

    private function createTokenMock(string $tokenString, \DateTimeImmutable $expiry): TokenInterface & MockObject
    {
        $mock = $this->createMock(TokenInterface::class);

        $mock
            ->method('toString')
            ->willReturn($tokenString);

        $mock
            ->method('getExpiryDateTime')
            ->willReturn($expiry);

        return $mock;
    }
}
