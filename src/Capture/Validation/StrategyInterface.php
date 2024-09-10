<?php

declare(strict_types=1);

namespace Vestiaire\Capture\Validation;

use Vestiaire\Authorisation\Token\TokenInterface;
use Vestiaire\Payment\Provider\ProviderInterface;

interface StrategyInterface
{
    /**
     * @throws \Vestiaire\Capture\Validation\Exception\Exception
     */
    public function validateTokenForProvider(TokenInterface $token, ProviderInterface $provider): void;
}