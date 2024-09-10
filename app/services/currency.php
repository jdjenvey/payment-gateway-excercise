<?php

declare(strict_types=1);

use Brick\Money\Currency;
use League\Container\Container;

/**
 * We are making an early assumption that all values are in Euros.
 * However, this should be made part of the API.
 *
 * A money value requires at least a scalar number and a currency code. A datetime needs to be
 * captured as well if we wish to do conversions retrospectively.
 *
 * @var Container $container
 */
$container->addShared(Currency::class, function () {
    return Currency::of('EUR');
});
