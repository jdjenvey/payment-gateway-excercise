<?php

declare(strict_types=1);

namespace Vestiaire\Payment\Provider\Weighting;

use Vestiaire\Payment\Provider\ProviderInterface;

interface WeightingInterface
{
    public function getWeight(): int;

    public function getProvider(): ProviderInterface;
}