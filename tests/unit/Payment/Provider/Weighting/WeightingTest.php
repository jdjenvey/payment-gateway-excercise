<?php

declare(strict_types=1);

namespace VestiaireTest\Unit\Payment\Provider\Weighting;

use PHPUnit\Framework\TestCase;
use Vestiaire\Payment\Provider\Weighting\Exception\WeightingTooLargelException;
use Vestiaire\Payment\Provider\Weighting\Exception\WeightingTooSmallException;
use Vestiaire\Payment\Provider\Weighting\Weighting;
use Vestiaire\Payment\Provider\ProviderInterface;
use PHPUnit\Framework\MockObject\MockObject;

class WeightingTest extends TestCase
{
    public function testConstructorThrowsExceptionForWeightBelow1(): void
    {
        $provider = $this->createProviderMock();
        $this->expectException(WeightingTooSmallException::class);

        new Weighting(0, $provider);
    }

    public function testConstructorThrowsExceptionForWeightOver100(): void
    {
        $provider = $this->createProviderMock();
        $this->expectException(WeightingTooLargelException::class);

        new Weighting(101, $provider);
    }

    public function testConstructorDoesNotThrowForValidWeight(): void
    {
        $provider = $this->createProviderMock();
        $weighting = new Weighting(5, $provider);

        $this->assertInstanceOf(Weighting::class, $weighting);
    }

    public function testGetWeightReturnsCorrectWeight(): void
    {
        $provider = $this->createProviderMock();
        $weighting = new Weighting(10, $provider);

        $this->assertSame(10, $weighting->getWeight());
    }

    public function testGetProviderReturnsProviderInstance(): void
    {
        $provider = $this->createProviderMock();
        $weighting = new Weighting(10, $provider);

        $this->assertSame($provider, $weighting->getProvider());
    }

    private function createProviderMock(): ProviderInterface & MockObject
    {
        return $this->createMock(ProviderInterface::class);
    }
}
