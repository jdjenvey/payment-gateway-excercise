<?php

declare(strict_types=1);

namespace VestiaireTest\Unit\Authorisation\Result;

use PHPUnit\Framework\MockObject\MockObject;
use Vestiaire\Authorisation\Result\AuthorisedHoldResult;
use Vestiaire\Authorisation\Result\InvalidCardAmountResult;
use Vestiaire\Authorisation\Result\ResultFactory;
use PHPUnit\Framework\TestCase;
use Vestiaire\Result\ResultInterface;
use Vestiaire\Authorisation\Token\TokenInterface;
use Vestiaire\Authorisation\Validation\Exception\Exception;

class ResultFactoryTest extends TestCase
{
    public function testCreateSuccessfulHoldResult()
    {
        $factory = new ResultFactory();

        $result = $factory->createSuccessfulHoldResult(
            $this->createTokenMock('foobar', false)
        );

        $this->assertInstanceOf(ResultInterface::class, $result);
        $this->assertInstanceOf(AuthorisedHoldResult::class, $result);
        $this->assertTrue($result->isValid());
    }

    public function testCreateCardAmountValidationErrorResult()
    {
        $factory = new ResultFactory();

        $error = 'An error occurred';

        $result = $factory->createCardAmountValidationErrorResult(
            $this->createExceptionMock($error, 400)
        );

        $this->assertInstanceOf(ResultInterface::class, $result);
        $this->assertInstanceOf(InvalidCardAmountResult::class, $result);
    }

    private function createTokenMock(string $encoded, bool $expired): TokenInterface&MockObject
    {
        $token = $this->createMock(TokenInterface::class);

        $token->method('toString')->willReturn($encoded);
        $token->method('isExpired')->willReturn($expired);

        return $token;
    }

    private function createExceptionMock(string $message, int $code): Exception
    {
        return new class ($message, $code) extends \Exception implements Exception
        {
            public function __construct(string $message, int $code)
            {
                parent::__construct($message, $code);
            }
        };
    }
}
