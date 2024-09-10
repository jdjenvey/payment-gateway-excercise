<?php

declare(strict_types=1);

namespace Vestiaire\Payment\Storage\Refund;

use Vestiaire\Payment\Storage\Refund\Exception\InvalidRefundIdException;

final readonly class PrefixedRefund implements RefundInterface
{
    private const string PATTERN = '/^rf\d{9}$/';

    public static function generate(): self
    {
        $values = ['rf'];

        for ($i = 0; $i < 9; $i++) {
            $values[] = (string)\random_int(0, 9);
        }

        return new self(
            \implode('', $values)
        );
    }

    public function __construct(
        private string $refundId
    )
    {
        if (!\preg_match(self::PATTERN, $this->refundId)) {
            throw new InvalidRefundIdException($this->refundId);
        }
    }

    public function getRefundId(): string
    {
        return $this->refundId;
    }
}
