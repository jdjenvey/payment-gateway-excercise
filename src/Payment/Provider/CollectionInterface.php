<?php

declare(strict_types=1);

namespace Vestiaire\Payment\Provider;

interface CollectionInterface
{
    public function selectRandom(): ProviderInterface;

    public function findByIdentifier(string $identifier): ?ProviderInterface;

    public function getIterator(): \Traversable;
}