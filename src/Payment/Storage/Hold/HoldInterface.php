<?php

declare(strict_types=1);

namespace Vestiaire\Payment\Storage\Hold;

interface HoldInterface
{
    public function getEncodedToken(): string;

    public function getCardNumber(): string;

    public function getAmount(): string;

    public function getCurrency(): string;

    public function isExpired(): bool;

    public function getExpiryDatetime(): \DateTimeInterface;
}