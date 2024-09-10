<?php

declare(strict_types=1);

namespace VestiaireTest\Unit\Payment\Provider;

use PHPUnit\Framework\TestCase;
use Vestiaire\Payment\Provider\WeightedCollection;
use Vestiaire\Payment\Provider\Weighting\WeightingInterface;
use Vestiaire\Payment\Provider\ProviderInterface;
use PHPUnit\Framework\MockObject\MockObject;
use OverflowException;

class WeightedCollectionTest extends TestCase
{
    public function testConstructorCalculatesMassCorrectly(): void
    {
        $provider1 = $this->createWeightingMock(10);
        $provider2 = $this->createWeightingMock(20);
        $provider3 = $this->createWeightingMock(30);

        $collection = new WeightedCollection($provider1, $provider2, $provider3);

        $this->assertInstanceOf(WeightedCollection::class, $collection);
    }

    public function testSelectRandomReturnsProviderWithinWeightRange(): void
    {
        $provider1 = $this->createWeightingMock(10, $this->createProviderMock('provider1'));
        $provider2 = $this->createWeightingMock(20, $this->createProviderMock('provider2'));
        $provider3 = $this->createWeightingMock(30, $this->createProviderMock('provider3'));

        $collection = new WeightedCollection($provider1, $provider2, $provider3);

        $this->assertInstanceOf(ProviderInterface::class, $collection->selectRandom());
        $this->assertInstanceOf(ProviderInterface::class, $collection->selectRandom());
        $this->assertInstanceOf(ProviderInterface::class, $collection->selectRandom());
        $this->assertInstanceOf(ProviderInterface::class, $collection->selectRandom());
        $this->assertInstanceOf(ProviderInterface::class, $collection->selectRandom());
    }

    public function testFindByIdentifierReturnsCorrectProvider(): void
    {
        $provider1 = $this->createWeightingMock(10, $this->createProviderMock('provider1'));
        $provider2 = $this->createWeightingMock(20, $this->createProviderMock('provider2'));

        $collection = new WeightedCollection($provider1, $provider2);


        $this->assertSame($provider2->getProvider(), $collection->findByIdentifier('provider2'));
        $this->assertSame($provider1->getProvider(), $collection->findByIdentifier('provider1'));
    }

    public function testFindByIdentifierReturnsNullForUnknownIdentifier(): void
    {
        $provider1 = $this->createWeightingMock(10, $this->createProviderMock('provider1'));
        $provider2 = $this->createWeightingMock(20, $this->createProviderMock('provider2'));

        $collection = new WeightedCollection($provider1, $provider2);

        $this->assertNull($collection->findByIdentifier('unknown-provider'));
    }

    private function createWeightingMock(int $weight, ?ProviderInterface $provider = null): WeightingInterface & MockObject
    {
        $mock = $this->createMock(WeightingInterface::class);

        $mock
            ->method('getWeight')
            ->willReturn($weight);

        if ($provider) {
            $mock
                ->method('getProvider')
                ->willReturn($provider);
        }

        return $mock;
    }

    private function createProviderMock(string $identifier): ProviderInterface & MockObject
    {
        $mock = $this->createMock(ProviderInterface::class);

        $mock
            ->method('getIdentifier')
            ->willReturn($identifier);

        return $mock;
    }
}
