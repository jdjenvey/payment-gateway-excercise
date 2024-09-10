<?php

declare(strict_types=1);

namespace Vestiaire\Result;

interface ResultInterface extends \JsonSerializable
{
    public function isValid(): bool;

    public function getStatusCode(): int;
}