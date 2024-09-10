<?php

declare(strict_types=1);

use League\Container\Container;
use Slim\Factory\AppFactory;
use Slim\Interfaces\RouteCollectorProxyInterface;

require __DIR__ . '/../vendor/autoload.php';

/**
 * As our app is developing, and we're not yet aiming for micro-optimisation, this simple bootstrapper
 * will dynamically import all of our routes and services for us.
 *
 * When we go to production we can use static includes to avoid iteration for tiny speed gains.
 * We should also set up Opcache preloading https://www.php.net/manual/en/opcache.preloading.php
 * as we are in a containerised environment.
 */
$container = new Container();
$app = AppFactory::create(container: $container);

$app->addErrorMiddleware(true, true, true);
$app->addBodyParsingMiddleware();

// Only expose $app and $container to the included files within the closure
$require = static function (string $path, RouteCollectorProxyInterface $app) use ($container): void {
    require_once $path;
};

// Load every file from the iterator
$loadAll = static function (RecursiveIteratorIterator $files, RouteCollectorProxyInterface $app) use ($require): void {
    foreach ($files as $file) {
        if ($file->isFile() && \pathinfo($file->getFilename(), PATHINFO_EXTENSION) === 'php') {
            $require($file->getRealPath(), $app);
        }
    }
};

// Create a recursive iterator for all files under the given path
$iterator = static function (string $path): \RecursiveIteratorIterator {
    return new \RecursiveIteratorIterator(
        new \RecursiveDirectoryIterator($path)
    );
};

// Load all services
$loadAll($iterator(__DIR__ . '/../app/services'), $app);

// Group all routes under v1 for now
$app->group('/v1', function (RouteCollectorProxyInterface $app) use ($loadAll, $iterator) {
    $loadAll($iterator(__DIR__ . '/../app/routes'), $app);
});

$app->run();
