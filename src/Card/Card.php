<?php

declare(strict_types=1);

namespace Vestiaire\Card;

use Vestiaire\Card\Exception\InvalidCvvException;
use Vestiaire\Card\Exception\InvalidExpirationDateException;
use Vestiaire\Card\Number\CardNumberInterface;

final readonly class Card implements CardInterface
{
    private \DateTimeInterface $expirationDate;

    public function __construct(
        private CardNumberInterface $number,
        private string  $cvv,
        string $expirationDate,
    )
    {
        if (!\preg_match('/^\d{3}$/', $this->cvv)) {
            throw new InvalidCvvException();
        }

        if (!\preg_match('/^\d{2}\/\d{2}$/', $expirationDate)) {
            throw new InvalidExpirationDateException();
        }

        list($month, $year) = \array_map(
            'intval',
            \explode(
                '/',
                $expirationDate
            )
        );

        if (!\checkdate($month, 1, $year)) {
            throw new InvalidExpirationDateException();
        }

        // FIXME: This method is vulnerable to a Y3K bug in case that matters in 976 years
        $this->expirationDate = new \DateTimeImmutable(
            \sprintf('20%02d-%02d-01T00:00:00', $year, $month)
        );
    }

    public function cardNumber(): CardNumberInterface
    {
        return $this->number;
    }

    public function cvv(): string
    {
        return $this->cvv;
    }

    public function expirationDate(): \DateTimeInterface
    {
        return $this->expirationDate;
    }
}
