<?php

declare(strict_types=1);

namespace Vestiaire\Payment\Storage\Hold;

use Brick\Money\Money;
use Vestiaire\Authorisation\Token\TokenInterface;
use Vestiaire\Card\CardInterface;

interface FactoryInterface
{
    public function forCardAmount(CardInterface $card, Money $amount, TokenInterface $token): HoldInterface;
}