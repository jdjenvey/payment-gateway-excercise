<?php

declare(strict_types=1);

namespace Vestiaire\Authorisation;

use Brick\Money\Money;
use Vestiaire\Authorisation\Token\FactoryInterface as TokenFactory;
use Vestiaire\Card\CardInterface;
use Vestiaire\Payment\Provider\ProviderInterface;
use Vestiaire\Payment\Storage\Hold\FactoryInterface;
use Vestiaire\Payment\Storage\Hold\HoldInterface;
use Vestiaire\Payment\Storage\StorageInterface;
use Vestiaire\Result\ResultInterface;

final readonly class Authoriser implements AuthorisationInterface
{
    public function __construct(
        private Result\FactoryInterface      $factory,
        private Validation\StrategyInterface $validator,
        private TokenFactory                 $tokens,
        private StorageInterface             $storage,
        private FactoryInterface             $holds,
    ) {}

    public function authorise(
        ProviderInterface $provider,
        CardInterface $card,
        Money $amount
    ): ResultInterface
    {
        try {
            $this->validator->validateCardAmount($card, $amount);
        } catch (Validation\Exception\Exception $exception) {
            return $this->factory->createCardAmountValidationErrorResult($exception);
        }

        $hold = $this->findHold($card, $amount);

        if ($hold) {
            return $this->factory->createSuccessfulHoldResult(
                $this->tokens->decodeToken(
                    $hold->getEncodedToken()
                )
            );
        }

        $hold = $this->holds->forCardAmount(
            $card,
            $amount,
            $this->tokens->createToken(
                $provider,
                $card,
                $amount
            )
        );

        $this->storage->storeHold($hold);

        return $this->factory->createSuccessfulHoldResult(
            $this->tokens->decodeToken(
                $hold->getEncodedToken()
            )
        );
    }

    private function findHold(
        CardInterface $card,
        Money $amount
    ): ?HoldInterface
    {
        $hold = $this->storage->findHold(
            $card->cardNumber()->__toString(),
            $amount->getAmount()->__toString(),
            $amount->getCurrency()->__toString()
        );

        if (!$hold) {
            return null;
        }

        if ($hold->isExpired()) {
            $this->storage->removeHold($hold);

            return null;
        }

        return $hold;
    }
}
