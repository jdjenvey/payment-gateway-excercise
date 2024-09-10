<?php

declare(strict_types=1);

namespace Vestiaire\Payment\Storage\Refund;

interface RefundInterface
{
    public function getRefundId(): string;
}