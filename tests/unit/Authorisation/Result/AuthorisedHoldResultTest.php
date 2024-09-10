<?php

declare(strict_types=1);

namespace VestiaireTest\Unit\Authorisation\Result;

use PHPUnit\Framework\TestCase;
use Vestiaire\Authorisation\Result\AuthorisedHoldResult;
use Vestiaire\Authorisation\Token\TokenInterface;
use PHPUnit\Framework\MockObject\MockObject;

class AuthorisedHoldResultTest extends TestCase
{
    public function testStatusCodeIsOk(): void
    {
        $tokenMock = $this->createTokenMockWithExpired(false);

        $result = new AuthorisedHoldResult($tokenMock);

        $this->assertSame(200, $result->getStatusCode());
    }

    public function testIsValidReturnsTrueWhenTokenIsNotExpired(): void
    {
        $tokenMock = $this->createTokenMockWithExpired(false);

        $result = new AuthorisedHoldResult($tokenMock);
        $isValid = $result->isValid();

        $this->assertTrue($isValid);
    }

    public function testIsValidReturnsFalseWhenTokenIsExpired(): void
    {
        $tokenMock = $this->createTokenMockWithExpired(true);

        $result = new AuthorisedHoldResult($tokenMock);
        $isValid = $result->isValid();

        $this->assertFalse($isValid);
    }

    public function testJsonSerializeReturnsCorrectArray(): void
    {
        $tokenMock = $this->createTokenMockWithTokenString('token-string');

        $result = new AuthorisedHoldResult($tokenMock);
        $serialized = $result->jsonSerialize();

        $this->assertSame(
            [
                'status' => 'success',
                'auth_token' => 'token-string',
            ],
            $serialized
        );
    }

    private function createTokenMock(): TokenInterface & MockObject
    {
        return $this->createMock(TokenInterface::class);
    }

    private function createTokenMockWithExpired(bool $isExpired): TokenInterface & MockObject
    {
        $tokenMock = $this->createMock(TokenInterface::class);

        $tokenMock->method('isExpired')->willReturn($isExpired);

        return $tokenMock;
    }

    private function createTokenMockWithTokenString(string $encoded): TokenInterface & MockObject
    {
        $tokenMock = $this->createMock(TokenInterface::class);

        $tokenMock->method('toString')->willReturn($encoded);

        return $tokenMock;
    }
}
