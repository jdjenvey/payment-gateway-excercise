<?php

declare(strict_types=1);

namespace Vestiaire\Capture\Validation;

use Vestiaire\Authorisation\Token\TokenInterface;
use Vestiaire\Payment\Provider\ProviderInterface;

final readonly class StrategyCollection implements StrategyInterface
{
    /** @var StrategyInterface[] */
    private array $strategies;

    public function __construct(StrategyInterface ...$strategies)
    {
        $this->strategies = $strategies;
    }

    public function validateTokenForProvider(TokenInterface $token, ProviderInterface $provider): void
    {
        foreach ($this->strategies as $strategy) {
            $strategy->validateTokenForProvider($token, $provider);
        }
    }
}
