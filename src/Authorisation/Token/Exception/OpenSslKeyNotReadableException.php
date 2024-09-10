<?php

declare(strict_types=1);

namespace Vestiaire\Authorisation\Token\Exception;

final class OpenSslKeyNotReadableException extends \RuntimeException implements Exception
{
    public function __construct(string $path)
    {
        parent::__construct(
            \sprintf("OpenSSL key not read from '%s'", $path),
            500
        );
    }
}
