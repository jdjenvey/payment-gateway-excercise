<?php

declare(strict_types=1);

use Brick\Money\Currency;
use League\Container\Container;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\App;
use Slim\Exception\HttpInternalServerErrorException;
use Vestiaire\Authorisation\Token\FactoryInterface;
use Brick\Money\Money;
use Vestiaire\Payment\Provider\CollectionInterface;

/**
 * @var App $app
 * @var Container $container
 */
$app->post('/capture', function (ServerRequestInterface $request, ResponseInterface $response) use ($container): ResponseInterface {
    $body = (array)$request->getParsedBody();

    /** @var FactoryInterface $tokens */
    $tokens = $container->get(FactoryInterface::class);

    try {
        $token = $tokens->decodeToken($body['auth_token'] ?? '');

        $amount = Money::of(
            (string)$body['amount'] ?? '',
            $container->get(Currency::class)
        );

        // There is no longer any reason to re-pass the amount as it's in the token, but we validate it here anyway.
        if ($amount->getAmount()->__toString() !== $token->getAmount()) {
            $response->getBody()->write(
                \json_encode([
                    'status' => 'error',
                    'message' => 'Token is not for the supplied amount',
                ])
            );

            return $response->withStatus(400);
        }

        $provider = $container->get(CollectionInterface::class)->findByIdentifier(
            $token->getProviderIdentifier()
        );

        if (null === $provider) {
            $response->getBody()->write(
                \json_encode([
                    'status' => 'error',
                    'message' => 'Token is not for any configured provider',
                ])
            );

            return $response->withStatus(400);
        }

        $result = $provider->capture($token);

        $response->getBody()->write(
            \json_encode($result)
        );

        return $response->withStatus($result->getStatusCode());
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
