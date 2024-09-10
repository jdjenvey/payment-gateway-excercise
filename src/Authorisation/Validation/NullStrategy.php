<?php

declare(strict_types=1);

namespace Vestiaire\Authorisation\Validation;

use Brick\Money\Money;
use Vestiaire\Card\CardInterface;

final class NullStrategy implements StrategyInterface
{
    public function validateCardAmount(CardInterface $card, Money $amount): void
    {
    }
}
