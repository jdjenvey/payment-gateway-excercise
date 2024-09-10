<?php

declare(strict_types=1);

use Brick\Money\Currency;
use League\Container\Container;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\App;
use Slim\Exception\HttpInternalServerErrorException;
use Vestiaire\Card\Card;
use Brick\Money\Money;
use Vestiaire\Payment\Provider\CollectionInterface;

/**
 * @var App $app
 * @var Container $container
 */
$app->post('/authorise', function (ServerRequestInterface $request, ResponseInterface $response) use ($container): ResponseInterface {
    $body = (array)$request->getParsedBody();

    try {
        $card = new Card(
            $body['card_number'] ?? '',
            $body['cvv'] ?? '',
            $body['expiry_date'] ?? '',
        );

        $amount = Money::of(
            (string)$body['amount'] ?? '',
            $container->get(Currency::class)
        );

        $provider = $container->get(CollectionInterface::class)->selectRandom();

        $result = $provider->authorise($card, $amount);

        $response->getBody()->write(
            \json_encode($result)
        );

        return $response->withStatus($result->getStatusCode());
    } catch (Vestiaire\Card\Exception\Exception $exception) {
        $response->getBody()->write(
            \json_encode([
                'status' => 'error',
                'message' => $exception->getMessage(),
            ])
        );

        return $response->withStatus($exception->getCode());
    } catch (Brick\Math\Exception\MathException $exception) {
        $response->getBody()->write(
            \json_encode([
                'status' => 'error',
                'message' => 'Unparsable monetary amount',
            ])
        );

        return $response->withStatus(400);
    } catch (\Throwable $exception) {
        throw new HttpInternalServerErrorException($request, '', $exception);
    }
});
