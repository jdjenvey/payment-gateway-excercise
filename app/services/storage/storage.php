<?php

declare(strict_types=1);

use League\Container\Container;
use Symfony\Component\Cache\Adapter\RedisAdapter;
use Vestiaire\Payment\Storage\CacheStorage;
use Vestiaire\Payment\Storage\StorageInterface;

/**
 * @var Container $container
 */
$container->addShared(StorageInterface::class, function () {
    return new CacheStorage(
        new RedisAdapter(
            RedisAdapter::createConnection('redis://localhost'),
        )
    );
});
