<?php

declare(strict_types=1);

namespace VestiaireTest\Unit\Authorisation\Validation;

use Brick\Money\Currency;
use PHPUnit\Framework\TestCase;
use Vestiaire\Authorisation\Validation\DigitMatchStrategy;
use Vestiaire\Authorisation\Validation\Exception\InvalidMatchValueException;
use Vestiaire\Authorisation\Validation\Exception\InvalidOffsetException;
use Vestiaire\Card\CardInterface;
use Brick\Money\Money;
use Vestiaire\Authorisation\Validation\Exception\InvalidCardDetailsException;
use PHPUnit\Framework\MockObject\MockObject;
use Vestiaire\Card\Number\CardNumberInterface;

class DigitMatchStrategyTest extends TestCase
{
    public function testThrowsExceptionWhenDigitOffsetGreaterThan15(): void
    {
        $this->expectException(InvalidOffsetException::class);

        new DigitMatchStrategy(16, 1);
    }

    public function testThrowsExceptionWhenDigitOffsetLessThan0(): void
    {
        $this->expectException(InvalidOffsetException::class);

        new DigitMatchStrategy(-1, 1);
    }

    public function testThrowsExceptionWhenMatchValueGreaterThan9(): void
    {
        $this->expectException(InvalidMatchValueException::class);

        new DigitMatchStrategy(5, 10);
    }

    public function testThrowsExceptionWhenMatchValueLessThan0(): void
    {
        $this->expectException(InvalidMatchValueException::class);

        new DigitMatchStrategy(5, -1);
    }

    public function testValidateCardAmountSucceedsWhenDigitMatches(): void
    {
        $card = $this->createCardMock('4111111111111111');
        $amount = $this->createMoneyFake();

        $strategy = new DigitMatchStrategy(0, 4);
        $strategy->validateCardAmount($card, $amount);

        $this->assertTrue(true); // No exception was thrown, test succeeds
    }

    public function testValidateCardAmountThrowsExceptionWhenDigitDoesNotMatch(): void
    {
        $card = $this->createCardMock('4111111111111111');
        $amount = $this->createMoneyFake();

        $strategy = new DigitMatchStrategy(0, 5);

        $this->expectException(InvalidCardDetailsException::class);

        $strategy->validateCardAmount($card, $amount);
    }

    private function createCardMock(string $cardNumber): CardInterface & MockObject
    {
        $mock = $this->createMock(CardInterface::class);
        $number = $this->createMock(CardNumberInterface::class);

        $number
            ->method('__toString')
            ->willReturn($cardNumber);

        $mock
            ->method('cardNumber')
            ->willReturn($number);

        return $mock;
    }

    private function createMoneyFake(): Money
    {
        return Money::of((string)\random_int(1, 999), Currency::of('EUR'));
    }
}

