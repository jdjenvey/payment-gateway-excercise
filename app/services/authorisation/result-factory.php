<?php

declare(strict_types=1);

use League\Container\Container;
use Vestiaire\Authorisation\Result\FactoryInterface;
use Vestiaire\Authorisation\Result\ResultFactory;

/**
 * @var Container $container
 */
$container->addShared(FactoryInterface::class, function (Container $container) {
    return new ResultFactory();
});
