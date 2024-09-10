<?php

declare(strict_types=1);

namespace Vestiaire\Payment\Provider;

use Brick\Money\Money;
use Vestiaire\Authorisation;
use Vestiaire\Capture;
use Vestiaire\Card\CardInterface;
use Vestiaire\Payment\Provider\Exception\InvalidProviderIdentifierException;
use Vestiaire\Payment\Storage\Transaction\TransactionInterface;
use Vestiaire\Refund;
use Vestiaire\Result\ResultInterface;

final readonly class ValidatingProvider implements ProviderInterface
{
    public function __construct(
        private string $identifier,
        private Authorisation\AuthorisationInterface $authoriser,
        private Capture\CaptureInterface $captor,
        private Refund\RefundInterface $refunder,
    )
    {
        if (!\preg_match('/^[a-z0-9\-_]+$/', $this->identifier)) {
            throw new InvalidProviderIdentifierException($this->identifier);
        }
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function authorise(CardInterface $card, Money $amount): ResultInterface
    {
        return $this->authoriser->authorise($this, $card, $amount);
    }

    public function capture(Authorisation\Token\TokenInterface $token): ResultInterface
    {
        return $this->captor->capture($this, $token);
    }

    public function refund(TransactionInterface $transaction, Money $amount): ResultInterface
    {
        return $this->refunder->refund($this, $transaction, $amount);
    }
}
