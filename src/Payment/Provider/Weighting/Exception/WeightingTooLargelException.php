<?php

declare(strict_types=1);

namespace Vestiaire\Payment\Provider\Weighting\Exception;

final class WeightingTooLargelException extends \OverflowException implements Exception
{
    public function __construct(int $weight)
    {
        parent::__construct(
            \sprintf(
                "Invalid weighting value {%d}. Weightings must be a positive integer <= 100",
                $weight
            )
        );
    }
}
