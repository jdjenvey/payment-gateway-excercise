<?php

declare(strict_types=1);

namespace Vestiaire\Capture\Result;

use Vestiaire\Capture\Validation\Exception\Exception;
use Vestiaire\Result\ResultInterface;

final readonly class InvalidTokenResult implements ResultInterface
{
    public function __construct(
        private Exception $exception
    ) {}

    public function isValid(): bool
    {
        return false;
    }

    public function getStatusCode(): int
    {
        return $this->exception->getCode();
    }

    public function jsonSerialize(): array
    {
        return [
            'status' => 'error',
            'message' => $this->exception->getMessage(),
        ];
    }
}
