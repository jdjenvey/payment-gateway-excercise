<?php

declare(strict_types=1);

namespace Vestiaire\Card\Exception;

final class InvalidCvvException extends \DomainException implements Exception
{
    protected $message = 'Invalid CVV';
}
