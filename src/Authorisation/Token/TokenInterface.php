<?php

declare(strict_types=1);

namespace Vestiaire\Authorisation\Token;

interface TokenInterface
{
    public function toString(): string;

    public function isExpired(): bool;

    public function getExpiryDateTime(): \DateTimeInterface;

    public function getProviderIdentifier(): string;

    public function getCardNumber(): string;

    public function getAmount(): string;

    public function getCurrency(): string;
}
