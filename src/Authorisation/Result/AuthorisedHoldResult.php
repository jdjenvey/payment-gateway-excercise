<?php

declare(strict_types=1);

namespace Vestiaire\Authorisation\Result;

use Vestiaire\Authorisation\Token\TokenInterface;
use Vestiaire\Result\ResultInterface;

final readonly class AuthorisedHoldResult implements ResultInterface
{
    public function __construct(
        private TokenInterface $token,
    ) {}

    public function isValid(): bool
    {
        return !$this->token->isExpired();
    }

    public function getStatusCode(): int
    {
        return 200;
    }

    public function jsonSerialize(): array
    {
        return [
            'status' => 'success',
            'auth_token' => $this->token->toString(),
        ];
    }
}
