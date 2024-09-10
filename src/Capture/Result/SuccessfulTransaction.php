<?php

declare(strict_types=1);

namespace Vestiaire\Capture\Result;

use Vestiaire\Payment\Storage\Transaction\TransactionInterface;
use Vestiaire\Result\ResultInterface;

final readonly class SuccessfulTransaction implements ResultInterface
{
    public function __construct(
        private TransactionInterface $transaction,
    ) {}

    public function isValid(): bool
    {
        return true;
    }

    public function getStatusCode(): int
    {
        return 200;
    }

    public function jsonSerialize(): array
    {
        return [
            'status' => 'success',
            'transaction_id' => $this->transaction->getTransactionId(),
        ];
    }
}
