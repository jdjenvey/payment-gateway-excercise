<?php

declare(strict_types=1);

namespace Vestiaire\Card\Exception;

final class InvalidExpirationDateException extends \DomainException implements Exception
{
    protected $message = 'Invalid expiration date';
}
