<?php

declare(strict_types=1);

namespace Vestiaire\Capture\Validation\Exception;

final class TokenExpiredException extends \LogicException implements Exception
{
    protected $message = 'Supplied token has expired';
}
