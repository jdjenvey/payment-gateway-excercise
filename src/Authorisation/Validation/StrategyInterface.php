<?php

declare(strict_types=1);

namespace Vestiaire\Authorisation\Validation;

use Brick\Money\Money;
use Vestiaire\Card\CardInterface;

interface StrategyInterface
{
    /**
     * @throws \Vestiaire\Authorisation\Validation\Exception\Exception
     */
    public function validateCardAmount(CardInterface $card, Money $amount): void;
}