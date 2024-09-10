<?php

declare(strict_types=1);

namespace VestiaireTest\Unit\Card\Number;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Vestiaire\Card\Number\Number;
use Vestiaire\Card\Number\Exception\InvalidCardNumberException;

class NumberTest extends TestCase
{
    #[DataProvider('invalidCardNumberProvider')]
    public function testConstructorThrowsExceptionForInvalidCardNumber(string $invalidNumber): void
    {
        $this->expectException(InvalidCardNumberException::class);

        new Number($invalidNumber);
    }

    #[DataProvider('validCardNumberProvider')]
    public function testConstructorDoesNotThrowExceptionForValidCardNumber(string $validNumber): void
    {
        $number = new Number($validNumber);

        $this->assertSame($validNumber, (string)$number);
    }

    public static function invalidCardNumberProvider(): array
    {
        return [
            'too short'            => ['12345678901234'],       // 14 digits
            'too long'             => ['12345678901234567'],    // 17 digits
            'non-digit characters' => ['1234abcd5678efgh'],     // Contains letters
            'spaces included'      => ['4111 1111 1111 1111'],  // Spaces in number
            'special characters'   => ['4111-1111-1111-1111'],  // Hyphen
            'utf8 characters'      => ['٤١١١١١١١١١١١١١١'],      // Arabic digits
            'empty string'         => [''],                     // Empty input
            'single digit'         => ['1'],                    // Single digit
            'alphanumeric'         => ['abcd1234efgh5678'],     // Alphanumeric string
        ];
    }

    public static function validCardNumberProvider(): array
    {
        return [
            'valid 16 digits'  => ['4111111111111111'], // Commonly used test card number
            'boundary test'    => ['1234567890123456'], // Exactly 16 digits, no formatting
            'boundary test 2'  => ['9999999999999999'], // Another valid 16-digit number
        ];
    }
}
