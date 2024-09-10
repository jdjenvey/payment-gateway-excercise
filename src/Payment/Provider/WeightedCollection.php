<?php

declare(strict_types=1);

namespace Vestiaire\Payment\Provider;

use Vestiaire\Payment\Provider\Weighting\WeightingInterface;

final class WeightedCollection implements CollectionInterface
{
    /** @var WeightingInterface[] */
    private array $providers;
    private int $mass;

    public function __construct(
        WeightingInterface $provider,
        WeightingInterface ...$providers
    )
    {
        $this->providers = \array_merge([$provider], $providers);

        $this->mass = \array_reduce(
            $this->providers,
            fn($total, WeightingInterface $item) => $total + $item->getWeight(),
            0
        );
    }

    public function selectRandom(): ProviderInterface
    {
        $selection = \rand(1, $this->mass);

        $current = 0;

        foreach ($this->providers as $weighting) {
            $current += $weighting->getWeight();

            if ($selection <= $current) {
                return $weighting->getProvider();
            }
        }
    }

    public function findByIdentifier(string $identifier): ?ProviderInterface
    {
        $match = \current(
            \array_filter($this->providers, function(WeightingInterface $weighting) use ($identifier): bool {
                return $weighting->getProvider()->getIdentifier() === $identifier;
            })
        );

        return $match instanceof WeightingInterface ? $match->getProvider() : null;
    }

    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->providers);
    }
}
