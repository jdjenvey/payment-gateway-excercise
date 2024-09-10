<?php

declare(strict_types=1);

namespace VestiaireTest\Unit\Payment\Storage\Transaction;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Vestiaire\Payment\Storage\Transaction\PrefixedTransaction;
use Vestiaire\Payment\Storage\Transaction\Exception\InvalidTransactionIdException;

class PrefixedTransactionTest extends TestCase
{
    public function testGenerateCreatesValidTransactionId(): void
    {
        $transaction = PrefixedTransaction::generate();

        $this->assertMatchesRegularExpression('/^tx\d{9}$/', $transaction->getTransactionId());
    }

    #[DataProvider('invalidTransactionIdProvider')]
    public function testConstructorThrowsExceptionForInvalidTransactionId(string $invalidTransactionId): void
    {
        $this->expectException(InvalidTransactionIdException::class);

        new PrefixedTransaction($invalidTransactionId);
    }

    #[DataProvider('validTransactionIdProvider')]
    public function testConstructorDoesNotThrowForValidTransactionId(string $validTransactionId): void
    {
        $transaction = new PrefixedTransaction($validTransactionId);

        $this->assertSame($validTransactionId, $transaction->getTransactionId());
    }

    public function testGetTransactionIdReturnsCorrectId(): void
    {
        $transaction = new PrefixedTransaction('tx987654321');

        $this->assertSame('tx987654321', $transaction->getTransactionId());
    }

    /**
     * Test for bug sometimes generating invalid values.
     */
    public function testGeneratingLargeNumbersOfRandomIdsDoesNotThrow(): void
    {
        for ($i = 0; $i < 1E3; $i++) {
            $this->assertInstanceOf(PrefixedTransaction::class, PrefixedTransaction::generate());
        }
    }

    public static function invalidTransactionIdProvider(): array
    {
        return [
            'no prefix' => ['123456789'],
            'too short' => ['tx12345'],
            'too long' => ['tx123456789123'],
            'non-numeric characters' => ['tx12abc456'],
            'missing tx prefix' => ['1234567890'],
            'wrong prefix' => ['xt1234567890'],
            'special characters' => ['tx12345!789'],
            'empty string' => [''],
        ];
    }

    public static function validTransactionIdProvider(): array
    {
        return [
            'valid transaction id 1' => ['tx123456789'],
            'valid transaction id 2' => ['tx987654321'],
        ];
    }
}
