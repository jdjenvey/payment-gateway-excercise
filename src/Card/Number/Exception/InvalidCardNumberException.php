<?php

declare(strict_types=1);

namespace Vestiaire\Card\Number\Exception;

final class InvalidCardNumberException extends \DomainException implements Exception
{
    public function __construct(string $number)
    {
        parent::__construct(
            \sprintf(
                "The card number '%s' is invalid.",
                $number
            )
        );
    }
}
