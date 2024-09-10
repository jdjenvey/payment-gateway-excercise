<?php

declare(strict_types=1);

namespace Vestiaire\Authorisation\Token;

use Brick\Money\Money;
use Vestiaire\Card\CardInterface;
use Vestiaire\Payment\Provider\ProviderInterface;

interface FactoryInterface
{
    public function createToken(ProviderInterface $provider, CardInterface $card, Money $amount): TokenInterface;

    public function decodeToken(string $encoded): TokenInterface;
}
