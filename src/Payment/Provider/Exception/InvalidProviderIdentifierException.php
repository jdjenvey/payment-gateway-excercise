<?php

declare(strict_types=1);

namespace Vestiaire\Payment\Provider\Exception;

final class InvalidProviderIdentifierException extends \InvalidArgumentException implements Exception
{
    public function __construct(string $identifier)
    {
        parent::__construct(
            \sprintf(
                "'%s' is not a valid provider identifier.",
                $identifier
            )
        );
    }
}
