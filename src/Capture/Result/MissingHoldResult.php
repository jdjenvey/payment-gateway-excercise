<?php

declare(strict_types=1);

namespace Vestiaire\Capture\Result;

use Vestiaire\Result\ResultInterface;

final readonly class MissingHoldResult implements ResultInterface
{
    public function isValid(): bool
    {
        return false;
    }

    public function getStatusCode(): int
    {
        return 404;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize(): array
    {
        return [
            'status' => 'error',
            'message' => 'Unable to find a hold for supplied token',
        ];
    }
}
