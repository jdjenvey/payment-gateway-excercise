<?php

declare(strict_types=1);

use Brick\Money\Currency;
use League\Container\Container;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\App;
use Slim\Exception\HttpInternalServerErrorException;
use Brick\Money\Money;
use Vestiaire\Payment\Provider\CollectionInterface;
use Vestiaire\Payment\Provider\ProviderInterface;
use Vestiaire\Payment\Storage\Transaction\PrefixedTransaction;

/**
 * @var App $app
 * @var Container $container
 */
$app->post('/refund', function (ServerRequestInterface $request, ResponseInterface $response) use ($container): ResponseInterface {
    $body = (array)$request->getParsedBody();

    try {
        $transaction = new PrefixedTransaction(
            $body['transaction_id'] ?? ''
        );

        $amount = Money::of(
            (string)$body['amount'] ?? '',
            $container->get(Currency::class)
        );

        $providers = $container->get(CollectionInterface::class);

        /** @var ProviderInterface $provider */
        foreach ($providers as $provider) {
            $result = $provider->refund($transaction, $amount);

            if (!$result->isValid()) {
                continue;
            }

            $response->getBody()->write(
                \json_encode($result)
            );

            return $response->withStatus($result->getStatusCode());
        }

        $response->getBody()->write(
            \json_encode([
                'status' => 'error',
                'message' => 'Transaction not found',
            ])
        );

        return $response->withStatus(404);
    } catch (Vestiaire\Exception\Exception $exception) {
        $response->getBody()->write(
            \json_encode([
                'status' => 'error',
                'message' => $exception->getMessage(),
            ])
        );

        return $response->withStatus($exception->getCode());
    } catch (\Throwable $exception) {
        throw new HttpInternalServerErrorException($request, '', $exception);
    }
});
