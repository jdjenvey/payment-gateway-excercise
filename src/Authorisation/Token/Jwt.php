<?php

declare(strict_types=1);

namespace Vestiaire\Authorisation\Token;

final readonly class Jwt implements TokenInterface
{
    public function __construct(
        private string $token,
        private \DateTimeInterface $expiresAt,
        private string $providerIdentifier,
        private string $cardNumber,
        private string $amount,
        private string $currency,
    ) {}

    public function toString(): string
    {
        return $this->token;
    }

    public function isExpired(): bool
    {
        return $this->expiresAt->getTimestamp() < \time();
    }

    public function getExpiryDateTime(): \DateTimeInterface
    {
        return $this->expiresAt;
    }

    public function getProviderIdentifier(): string
    {
        return $this->providerIdentifier;
    }

    public function getCardNumber(): string
    {
        return $this->cardNumber;
    }

    public function getAmount(): string
    {
        return $this->amount;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }
}
