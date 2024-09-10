<?php

declare(strict_types=1);

namespace Vestiaire\Authorisation\Result;

use Vestiaire\Result\ResultInterface;
use Vestiaire\Authorisation\Token\TokenInterface;
use Vestiaire\Authorisation\Validation\Exception\Exception;

interface FactoryInterface
{
    public function createSuccessfulHoldResult(TokenInterface $token): ResultInterface;

    public function createCardAmountValidationErrorResult(Exception $exception): ResultInterface;
}