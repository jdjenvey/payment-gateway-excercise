<?php

declare(strict_types=1);

namespace VestiaireTest\Unit\Payment\Storage\Refund;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Vestiaire\Payment\Storage\Refund\PrefixedRefund;
use Vestiaire\Payment\Storage\Refund\Exception\InvalidRefundIdException;

class PrefixedRefundTest extends TestCase
{
    public function testGenerateCreatesValidRefundId(): void
    {
        $refund = PrefixedRefund::generate();

        $this->assertMatchesRegularExpression('/^rf\d{9}$/', $refund->getRefundId());
    }

    #[DataProvider('invalidRefundIdProvider')]
    public function testConstructorThrowsExceptionForInvalidRefundId(string $invalidRefundId): void
    {
        $this->expectException(InvalidRefundIdException::class);

        new PrefixedRefund($invalidRefundId);
    }

    #[DataProvider('validRefundIdProvider')]
    public function testConstructorDoesNotThrowForValidRefundId(string $validRefundId): void
    {
        $refund = new PrefixedRefund($validRefundId);

        $this->assertSame($validRefundId, $refund->getRefundId());
    }

    /**
     * Test for bug sometimes generating invalid values.
     */
    public function testGeneratingLargeNumbersOfRandomIdsDoesNotThrow(): void
    {
        for ($i = 0; $i < 1E3; $i++) {
            $this->assertInstanceOf(PrefixedRefund::class, PrefixedRefund::generate());
        }
    }

    public static function invalidRefundIdProvider(): array
    {
        return [
            'no prefix'                 => ['123456789'],
            'too short'                 => ['rf12345'],
            'too long'                  => ['rf123456789123'],
            'non-numeric characters'    => ['rf12abc456'],
            'missing rf prefix'         => ['1234567890'],
            'wrong prefix'              => ['fr1234567890'],
            'special characters'        => ['rf12345!789'],
            'empty string'              => [''],
        ];
    }

    public static function validRefundIdProvider(): array
    {
        return [
            'valid refund id 1' => ['rf123456789'],
            'valid refund id 2' => ['rf987654321'],
        ];
    }
}
