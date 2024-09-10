<?php

declare(strict_types=1);

namespace VestiaireTest\Unit\Capture\Result;

use PHPUnit\Framework\TestCase;
use Vestiaire\Capture\Result\InvalidTokenResult;
use Vestiaire\Capture\Validation\Exception\Exception;
use PHPUnit\Framework\MockObject\MockObject;

class InvalidTokenResultTest extends TestCase
{
    public function testIsValidReturnsFalse(): void
    {
        $exception = $this->createExceptionMock();
        $result = new InvalidTokenResult($exception);

        $this->assertFalse($result->isValid());
    }

    public function testGetStatusCodeReturnsExceptionCode(): void
    {
        $exception = $this->createExceptionMock(400);
        $result = new InvalidTokenResult($exception);

        $this->assertSame(400, $result->getStatusCode());
    }

    public function testJsonSerializeReturnsCorrectData(): void
    {
        $exception = $this->createExceptionMock(400, 'Invalid token');
        $result = new InvalidTokenResult($exception);

        $expected = [
            'status' => 'error',
            'message' => 'Invalid token',
        ];

        $this->assertSame($expected, $result->jsonSerialize());
    }

    private function createExceptionMock(int $code = 400, string $message = 'Error'): Exception
    {
        return new class ($message, $code) extends \Exception implements Exception {
        };
    }
}
