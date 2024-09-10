<?php

declare(strict_types=1);

namespace Vestiaire\Authorisation\Token\Exception;

final class OpenSslKeyNotFoundException extends \InvalidArgumentException implements Exception
{
    public function __construct(string $path)
    {
        parent::__construct(
            \sprintf("OpenSSL key not found at '%s'", $path),
            500
        );
    }
}
