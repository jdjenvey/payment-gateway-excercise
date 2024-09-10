<?php

declare(strict_types=1);

namespace Vestiaire\Capture;

use Vestiaire\Authorisation\Token\TokenInterface;
use Vestiaire\Capture\Validation\StrategyInterface;
use Vestiaire\Payment\Provider\ProviderInterface;
use Vestiaire\Payment\Storage\StorageInterface;
use Vestiaire\Payment\Storage\Transaction\PrefixedTransaction;
use Vestiaire\Result\ResultInterface;

final readonly class Captor implements CaptureInterface
{
    public function __construct(
        private Result\FactoryInterface $factory,
        private StrategyInterface $validator,
        private StorageInterface $storage,
    ) {}

    public function capture(ProviderInterface $provider, TokenInterface $token): ResultInterface
    {
        try {
            $this->validator->validateTokenForProvider($token, $provider);
        } catch (Validation\Exception\Exception $exception) {
            return $this->factory->createInvalidTokenErrorResult($exception);
        }

        $hold = $this->storage->findHold(
            $token->getCardNumber(),
            $token->getAmount(),
            $token->getCurrency(),
        );

        if (null === $hold) {
            return $this->factory->createMissingHoldErrorResult();
        }

        $transaction = PrefixedTransaction::generate();

        $this->storage->storeTransaction($transaction);
        $this->storage->removeHold($hold);

        return $this->factory->createSuccessfulTransactionResult($transaction);
    }
}
