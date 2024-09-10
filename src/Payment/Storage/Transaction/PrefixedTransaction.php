<?php

declare(strict_types=1);

namespace Vestiaire\Payment\Storage\Transaction;

use Vestiaire\Payment\Storage\Transaction\Exception\InvalidTransactionIdException;

final readonly class PrefixedTransaction implements TransactionInterface
{
    private const string PATTERN = '/^tx\d{9}$/';

    public static function generate(): self
    {
        $values = ['tx'];

        for ($i = 0; $i < 9; $i++) {
            $values[] = (string)\random_int(0, 9);
        }

        return new self(
            \implode('', $values)
        );
    }

    public function __construct(
        private string $transactionId
    )
    {
        if (!\preg_match(self::PATTERN, $this->transactionId)) {
            throw new InvalidTransactionIdException($this->transactionId);
        }
    }

    public function getTransactionId(): string
    {
        return $this->transactionId;
    }
}
