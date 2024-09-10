<?php

declare(strict_types=1);

namespace Vestiaire\Capture\Validation\Exception;

final class ProviderMismatchException extends \LogicException implements Exception
{
    protected $message = 'Supplied token is not for this provider';
}
