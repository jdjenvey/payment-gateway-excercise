<?php

declare(strict_types=1);

namespace Vestiaire\Authorisation\Validation\Exception;

final class InvalidMatchValueException extends \InvalidArgumentException implements Exception
{
    protected $message = 'Matching digit must be between 0-9.';
    protected $code = 500;
}
