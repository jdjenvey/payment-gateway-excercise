<?php

declare(strict_types=1);

use League\Container\Container;
use Vestiaire\Payment\Storage\Hold\Factory;
use Vestiaire\Payment\Storage\Hold\FactoryInterface;

/**
 * @var Container $container
 */
$container->addShared(FactoryInterface::class, function () {
    return new Factory();
});
