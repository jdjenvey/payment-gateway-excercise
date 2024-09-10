<?php

declare(strict_types=1);

use League\Container\Container;
use Vestiaire\Authorisation\Authoriser;
use Vestiaire\Authorisation\Validation\DigitMatchStrategy;
use Vestiaire\Authorisation\Validation\NullStrategy;
use Vestiaire\Capture\Captor;
use Vestiaire\Capture\Validation\StrategyCollection;
use Vestiaire\Capture\Validation\TokenIsForProviderStrategy;
use Vestiaire\Capture\Validation\TokenNotExpiredStrategy;
use Vestiaire\Payment\Provider\CollectionInterface;
use Vestiaire\Payment\Provider\ValidatingProvider;
use Vestiaire\Payment\Provider\WeightedCollection;
use Vestiaire\Payment\Provider\Weighting\Weighting;
use Vestiaire\Refund\Refunder;

/**
 * @var Container $container
 */
$container->addShared(CollectionInterface::class, function (Container $container) {
    $captor = new Captor(
        $container->get(Vestiaire\Capture\Result\FactoryInterface::class),
        new StrategyCollection(
            new TokenIsForProviderStrategy(),
            new TokenNotExpiredStrategy(),
        ),
        $container->get(Vestiaire\Payment\Storage\StorageInterface::class),
    );

    $refunder = new Refunder(
        $container->get(Vestiaire\Refund\Result\FactoryInterface::class),
        $container->get(Vestiaire\Payment\Storage\StorageInterface::class),
    );

    $providerA = new Weighting(
        6,
        new ValidatingProvider(
            'blastercard',
            new Authoriser(
                $container->get(Vestiaire\Authorisation\Result\FactoryInterface::class),
                new DigitMatchStrategy(0, 4),
                $container->get(Vestiaire\Authorisation\Token\FactoryInterface::class),
                $container->get(Vestiaire\Payment\Storage\StorageInterface::class),
                $container->get(Vestiaire\Payment\Storage\Hold\FactoryInterface::class),
            ),
            $captor,
            $refunder
        )
    );

    $providerB = new Weighting(
        4,
        new ValidatingProvider(
            'tripe',
            new Authoriser(
                $container->get(Vestiaire\Authorisation\Result\FactoryInterface::class),
                new NullStrategy(),
                $container->get(Vestiaire\Authorisation\Token\FactoryInterface::class),
                $container->get(Vestiaire\Payment\Storage\StorageInterface::class),
                $container->get(Vestiaire\Payment\Storage\Hold\FactoryInterface::class),
            ),
            $captor,
            $refunder
        )
    );

    return new WeightedCollection(
        $providerA,
        $providerB
    );
});
