<?php

declare(strict_types=1);

namespace Vestiaire\Payment\Storage\Hold;

use Brick\Money\Money;
use Vestiaire\Authorisation\Token\TokenInterface;
use Vestiaire\Card\CardInterface;

final class Factory implements FactoryInterface
{
    public function forCardAmount(
        CardInterface $card,
        Money $amount,
        TokenInterface $token,
    ): HoldInterface
    {
        return new Hold(
            $card->cardNumber()->__toString(),
            $amount->getAmount()->__toString(),
            $amount->getCurrency()->getCurrencyCode(),
            $token->toString(),
            $token->getExpiryDateTime(),
        );
    }
}
