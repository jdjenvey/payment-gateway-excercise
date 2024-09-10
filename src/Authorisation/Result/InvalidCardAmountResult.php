<?php

declare(strict_types=1);

namespace Vestiaire\Authorisation\Result;

use Vestiaire\Authorisation\Validation\Exception\Exception;
use Vestiaire\Result\ResultInterface;

final readonly class InvalidCardAmountResult implements ResultInterface
{
    public function __construct(
        private Exception $exception,
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
