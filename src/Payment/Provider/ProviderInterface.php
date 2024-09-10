<?php

declare(strict_types=1);

namespace Vestiaire\Payment\Provider;

use Brick\Money\Money;
use Vestiaire\Card\CardInterface;
use Vestiaire\Result\ResultInterface;
use Vestiaire\Payment\Storage\Transaction\TransactionInterface;
use Vestiaire\Authorisation\Token\TokenInterface;

interface ProviderInterface
{
    public function getIdentifier(): string;

    public function authorise(CardInterface $card, Money $amount): ResultInterface;

    public function capture(TokenInterface $token): ResultInterface;

    public function refund(TransactionInterface $transaction, Money $amount): ResultInterface;
}
