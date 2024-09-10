<?php

declare(strict_types=1);

namespace Vestiaire\Capture;

use Vestiaire\Authorisation\Token\TokenInterface;
use Vestiaire\Payment\Provider\ProviderInterface;
use Vestiaire\Result\ResultInterface;

interface CaptureInterface
{
    public function capture(ProviderInterface $provider, TokenInterface $token): ResultInterface;
}