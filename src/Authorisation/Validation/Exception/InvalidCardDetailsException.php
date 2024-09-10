<?php

declare(strict_types=1);

namespace Vestiaire\Authorisation\Validation\Exception;

final class InvalidCardDetailsException extends \DomainException implements Exception
{
    protected $message = 'Invalid card details';
    protected $code = 400;
}
