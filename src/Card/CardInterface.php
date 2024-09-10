<?php

declare(strict_types=1);

namespace Vestiaire\Card;

use Vestiaire\Card\Number\CardNumberInterface;

interface CardInterface
{
    public function cardNumber(): CardNumberInterface;

    public function cvv(): string;

    public function expirationDate(): \DateTimeInterface;
}
