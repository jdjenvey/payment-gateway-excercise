<?php

declare(strict_types=1);

namespace VestiaireTest\Unit\Card;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Vestiaire\Card\Card;
use Vestiaire\Card\Exception\InvalidCvvException;
use Vestiaire\Card\Exception\InvalidExpirationDateException;
use Vestiaire\Card\Number\CardNumberInterface;

class CardTest extends TestCase
{
    public function testCardNumberIsReturned()
    {
        $number = $this->createCardNumberMock();

        $card = new Card($number, '564', '12/30');

        $this->assertSame($number, $card->cardNumber());
    }

    #[DataProvider('invalidCvvProvider')]
    public function testConstructorThrowsExceptionForInvalidCvv(string $invalidCvv): void
    {
        $number = $this->createCardNumberMock();
        $this->expectException(InvalidCvvException::class);

        new Card($number, $invalidCvv, '12/24');
    }

    #[DataProvider('invalidExpirationDateProvider')]
    public function testConstructorThrowsExceptionForInvalidExpirationDate(string $invalidDate): void
    {
        $number = $this->createCardNumberMock();
        $this->expectException(InvalidExpirationDateException::class);

        new Card($number, '123', $invalidDate);
    }

    #[DataProvider('validCardDataProvider')]
    public function testConstructorDoesNotThrowExceptionForValidCard(
        string $validCvv,
        string $validExpirationDate,
        string $expectedDate,
    ): void
    {
        $number = $this->createCardNumberMock();
        $card = new Card($number, $validCvv, $validExpirationDate);

        $this->assertSame($validCvv, $card->cvv());
        $this->assertEquals(new \DateTimeImmutable($expectedDate), $card->expirationDate());
    }

    public static function invalidCvvProvider(): array
    {
        return [
            'too short'      => ['12'],          // Less than 3 digits
            'too long'       => ['1234'],        // More than 3 digits
            'letters'        => ['abc'],         // Alphabetical characters
            'alphanumeric'   => ['12a'],         // Alphanumeric string
            'special chars'  => ['12!'],         // Special characters
            'empty string'   => [''],            // Empty input
        ];
    }

    public static function invalidExpirationDateProvider(): array
    {
        return [
            'wrong format'      => ['2024/12'],  // Wrong date format
            'too short'         => ['1/24'],     // Too short (month should be two digits)
            'month 13'          => ['13/24'],    // Invalid month (greater than 12)
            'month 0'           => ['00/24'],    // Invalid month (greater than 12)
            'non-numeric'       => ['aa/bb'],    // Non-numeric characters
            'empty string'      => [''],         // Empty string
            'month as zero'     => ['00/24'],    // Zero as month
        ];
    }

    public static function validCardDataProvider(): array
    {
        return [
            'valid card'    => ['123', '12/24', '2024-12-01T00:00:00'],
            'boundary test' => ['999', '01/23', '2023-01-01T00:00:00'],
            'expired'       => ['201', '02/19', '2019-02-01T00:00:00'],
        ];
    }

    private function createCardNumberMock(): CardNumberInterface
    {
        return $this->createMock(CardNumberInterface::class);
    }
}
