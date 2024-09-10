<?php

declare(strict_types=1);

use League\Container\Container;
use Vestiaire\Authorisation\Token\FactoryInterface;
use Vestiaire\Authorisation\Token\JwtFactory;

/**
 * @var Container $container
 */
$container->addShared(FactoryInterface::class, function () {
    return new JwtFactory(
        __DIR__ . '/../../keys/private.pem',
        __DIR__ . '/../../keys/public.pem',
        $_SERVER['HTTP_HOST']
    );
});
