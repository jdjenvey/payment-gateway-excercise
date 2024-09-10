<?php

declare(strict_types=1);

namespace Vestiaire\Card\Number;

use Vestiaire\Card\Number\Exception\InvalidCardNumberException;

final readonly class Number implements CardNumberInterface
{
    public function __construct(
        private string $number,
    )
    {
        if (!\preg_match('/^\d{16}$/', $this->number)) {
            throw new InvalidCardNumberException('Invalid card number');
        }
    }

    public function __toString(): string
    {
        return $this->number;
    }
}
