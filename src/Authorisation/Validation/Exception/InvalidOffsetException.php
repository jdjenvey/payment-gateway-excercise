<?php

declare(strict_types=1);

namespace Vestiaire\Authorisation\Validation\Exception;

final class InvalidOffsetException extends \OverflowException implements Exception
{
    protected $message = 'Card number offsets must be between 0-15';
    protected $code = 500;
}
