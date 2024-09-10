<?php

declare(strict_types=1);

namespace Vestiaire\Payment\Storage\Hold;

final readonly class Hold implements HoldInterface
{
    public function __construct(
        private string $cardNumber,
        private string $amount,
        private string $currency,
        private string $token,
        private \DateTimeInterface $expires,
    ) {}

    public function getEncodedToken(): string
    {
        return $this->token;
    }

    public function getAmount(): string
    {
        return $this->amount;
    }

    public function getCardNumber(): string
    {
        return $this->cardNumber;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function isExpired(): bool
    {
        return $this->expires->getTimestamp() <= \time();
    }

    public function getExpiryDatetime(): \DateTimeInterface
    {
        return $this->expires;
    }
}
