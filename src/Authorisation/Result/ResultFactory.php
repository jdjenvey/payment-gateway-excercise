<?php

declare(strict_types=1);

namespace Vestiaire\Authorisation\Result;

use Vestiaire\Authorisation\Token\TokenInterface;
use Vestiaire\Authorisation\Validation\Exception\Exception;
use Vestiaire\Result\ResultInterface;

final class ResultFactory implements FactoryInterface
{
    public function createSuccessfulHoldResult(TokenInterface $token): ResultInterface
    {
        return new AuthorisedHoldResult($token);
    }

    public function createCardAmountValidationErrorResult(Exception $exception): ResultInterface
    {
        return new InvalidCardAmountResult($exception);
    }
}
