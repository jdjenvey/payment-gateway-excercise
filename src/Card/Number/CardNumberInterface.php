<?php

declare(strict_types=1);

namespace Vestiaire\Card\Number;

interface CardNumberInterface
{
    public function __toString(): string;
}