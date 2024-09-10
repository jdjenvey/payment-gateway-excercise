<?php

declare(strict_types=1);

namespace Vestiaire\Capture\Validation;

use Vestiaire\Authorisation\Token\TokenInterface;
use Vestiaire\Capture\Validation\Exception\ProviderMismatchException;
use Vestiaire\Payment\Provider\ProviderInterface;

final class TokenIsForProviderStrategy implements StrategyInterface
{
    public function validateTokenForProvider(TokenInterface $token, ProviderInterface $provider): void
    {
        if ($token->getProviderIdentifier() !== $provider->getIdentifier()) {
            throw new ProviderMismatchException();
        }
    }
}
