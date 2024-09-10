<?php

declare(strict_types=1);

namespace Vestiaire\Authorisation\Validation;

use Brick\Money\Money;
use Vestiaire\Authorisation\Validation\Exception\InvalidCardDetailsException;
use Vestiaire\Authorisation\Validation\Exception\InvalidMatchValueException;
use Vestiaire\Authorisation\Validation\Exception\InvalidOffsetException;
use Vestiaire\Card\CardInterface;

final class DigitMatchStrategy implements StrategyInterface
{
    public function __construct(
        private readonly int $digitOffset,
        private readonly int $matchValue,
    )
    {
        if ($this->digitOffset > 15) {
            throw new InvalidOffsetException();
        }

        if ($this->digitOffset < 0) {
            throw new InvalidOffsetException();
        }

        if ($this->matchValue > 9) {
            throw new InvalidMatchValueException();
        }

        if ($this->matchValue < 0) {
            throw new InvalidMatchValueException();
        }
    }

    public function validateCardAmount(CardInterface $card, Money $amount): void
    {
        $digit = (int)\substr($card->cardNumber()->__toString(), $this->digitOffset, 1);

        if ($digit !== $this->matchValue) {
            throw new InvalidCardDetailsException();
        }
    }
}
