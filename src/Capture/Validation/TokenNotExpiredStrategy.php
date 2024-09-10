<?php

declare(strict_types=1);

namespace Vestiaire\Capture\Validation;

use Vestiaire\Authorisation\Token\TokenInterface;
use Vestiaire\Capture\Validation\Exception\TokenExpiredException;
use Vestiaire\Payment\Provider\ProviderInterface;

final class TokenNotExpiredStrategy implements StrategyInterface
{
    public function validateTokenForProvider(TokenInterface $token, ProviderInterface $provider): void
    {
        if ($token->isExpired()) {
            throw new TokenExpiredException();
        }
    }
}
