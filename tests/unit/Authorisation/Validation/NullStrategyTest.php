<?php

declare(strict_types=1);

namespace VestiaireTest\Unit\Authorisation\Validation;

use Brick\Money\Currency;
use Brick\Money\Money;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Vestiaire\Authorisation\Validation\NullStrategy;
use Vestiaire\Card\CardInterface;
use Vestiaire\Card\Number\CardNumberInterface;

class NullStrategyTest extends TestCase
{
    public function testNullStrategyDoesNothing(): void
    {
        $strategy = new NullStrategy();

        $this->assertNull(
            $strategy->validateCardAmount(
                $this->createCardMock('0987654321357906'),
                Money::of('123.45', Currency::of('EUR'))
            )
        );
    }


    private function createCardMock(string $number): CardInterface & MockObject
    {
        $card = $this->createMock(CardInterface::class);
        $cardNumber = $this->createMock(CardNumberInterface::class);

        $cardNumber
            ->method('__toString')
            ->willReturn($number);

        $card
            ->method('cardNumber')
            ->willReturn($cardNumber);

        return $card;
    }
}
