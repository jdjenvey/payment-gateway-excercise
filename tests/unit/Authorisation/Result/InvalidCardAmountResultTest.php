<?php

declare(strict_types=1);

namespace VestiaireTest\Unit\Authorisation\Result;

use PHPUnit\Framework\TestCase;
use Vestiaire\Authorisation\Result\InvalidCardAmountResult;
use Vestiaire\Authorisation\Validation\Exception\Exception;

class InvalidCardAmountResultTest extends TestCase
{
    public function testStatusCodeMatchesException(): void
    {
        $exception = $this->createExceptionMock('foo', 666);

        $result = new InvalidCardAmountResult($exception);

        $this->assertSame(666, $result->getStatusCode());
    }

    public function testIsValidReturnsFalse(): void
    {
        $exception = $this->createExceptionMock('foo', 400);

        $result = new InvalidCardAmountResult($exception);
        $isValid = $result->isValid();

        $this->assertFalse($isValid);
    }

    public function testJsonSerializeReturnsCorrectArray(): void
    {
        $exception = $this->createExceptionMock('Invalid card amount', 400);

        $result = new InvalidCardAmountResult($exception);
        $serialized = $result->jsonSerialize();

        $this->assertSame(
            [
                'status' => 'error',
                'message' => 'Invalid card amount',
            ],
            $serialized
        );
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
