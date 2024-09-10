<?php

declare(strict_types=1);

namespace Vestiaire\Payment\Provider\Weighting\Exception;

final class WeightingTooSmallException extends \InvalidArgumentException implements Exception
{
    public function __construct(int $weight)
    {
        parent::__construct(
            \sprintf(
                "Invalid weighting value {%d}. Weightings must be a positive integer > 0",
                $weight
            )
        );
    }
}
