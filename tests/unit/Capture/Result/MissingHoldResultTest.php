<?php

declare(strict_types=1);

namespace VestiaireTest\Unit\Capture\Result;

use PHPUnit\Framework\TestCase;
use Vestiaire\Capture\Result\MissingHoldResult;

class MissingHoldResultTest extends TestCase
{
    public function testIsValidReturnsFalse(): void
    {
        $result = new MissingHoldResult();

        $this->assertFalse($result->isValid());
    }

    public function testGetStatusCodeReturns404(): void
    {
        $result = new MissingHoldResult();

        $this->assertSame(404, $result->getStatusCode());
    }

    public function testJsonSerializeReturnsCorrectData(): void
    {
        $result = new MissingHoldResult();

        $expected = [
            'status' => 'error',
            'message' => 'Unable to find a hold for supplied token',
        ];

        $this->assertSame($expected, $result->jsonSerialize());
    }
}
