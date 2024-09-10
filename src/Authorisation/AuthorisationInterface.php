<?php

declare(strict_types=1);

namespace Vestiaire\Authorisation;

use Brick\Money\Money;
use Vestiaire\Card\CardInterface;
use Vestiaire\Payment\Provider\ProviderInterface;
use Vestiaire\Result\ResultInterface;

interface AuthorisationInterface
{
    public function authorise(ProviderInterface $provider, CardInterface $card, Money $amount): ResultInterface;
}