<?php

declare(strict_types=1);

namespace Vestiaire\Payment\Provider\Weighting;

use Vestiaire\Payment\Provider\ProviderInterface;
use Vestiaire\Payment\Provider\Weighting\Exception\WeightingTooLargelException;
use Vestiaire\Payment\Provider\Weighting\Exception\WeightingTooSmallException;

final readonly class Weighting implements WeightingInterface
{
    public function __construct(
        private int $weight,
        private ProviderInterface $provider
    )
    {
        if ($this->weight < 1) {
            throw new WeightingTooSmallException($weight);
        }

        if ($this->weight > 100) {
            throw new WeightingTooLargelException($weight);
        }
    }

    public function getWeight(): int
    {
        return $this->weight;
    }

    public function getProvider(): ProviderInterface
    {
        return $this->provider;
    }
}
