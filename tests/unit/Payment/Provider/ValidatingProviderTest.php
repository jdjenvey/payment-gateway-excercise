<?php

declare(strict_types=1);

namespace VestiaireTest\Unit\Payment\Provider;

use Brick\Money\Currency;
use PHPUnit\Framework\TestCase;
use Brick\Money\Money;
use Vestiaire\Payment\Provider\ValidatingProvider;
use Vestiaire\Authorisation\AuthorisationInterface;
use Vestiaire\Capture\CaptureInterface;
use Vestiaire\Refund\RefundInterface;
use Vestiaire\Authorisation\Token\TokenInterface;
use Vestiaire\Payment\Provider\Exception\InvalidProviderIdentifierException;
use Vestiaire\Card\CardInterface;
use Vestiaire\Payment\Storage\Transaction\TransactionInterface;
use Vestiaire\Result\ResultInterface;
use PHPUnit\Framework\MockObject\MockObject;

class ValidatingProviderTest extends TestCase
{
    public function testConstructorThrowsExceptionForInvalidIdentifier(): void
    {
        $authoriser = $this->createAuthorisationMock();
        $captor = $this->createCaptorMock();
        $refunder = $this->createRefundMock();

        $this->expectException(InvalidProviderIdentifierException::class);

        new ValidatingProvider('Invalid-Identifier!', $authoriser, $captor, $refunder);
    }

    public function testConstructorDoesNotThrowForValidIdentifier(): void
    {
        $authoriser = $this->createAuthorisationMock();
        $captor = $this->createCaptorMock();
        $refunder = $this->createRefundMock();

        $provider = new ValidatingProvider('valid-identifier', $authoriser, $captor, $refunder);

        $this->assertInstanceOf(ValidatingProvider::class, $provider);
    }

    public function testGetIdentifierReturnsCorrectIdentifier(): void
    {
        $authoriser = $this->createAuthorisationMock();
        $captor = $this->createCaptorMock();
        $refunder = $this->createRefundMock();

        $provider = new ValidatingProvider('valid-identifier', $authoriser, $captor, $refunder);

        $this->assertSame('valid-identifier', $provider->getIdentifier());
    }

    public function testAuthoriseCallsAuthoriser(): void
    {
        $authoriser = $this->createAuthorisationMock();
        $captor = $this->createCaptorMock();
        $refunder = $this->createRefundMock();
        $card = $this->createCardMock();
        $money = $this->createMoneyFake();
        $result = $this->createResultMock();

        $authoriser
            ->expects($this->once())
            ->method('authorise')
            ->with($this->isInstanceOf(ValidatingProvider::class), $card, $money)
            ->willReturn($result);

        $provider = new ValidatingProvider('valid-identifier', $authoriser, $captor, $refunder);

        $this->assertSame($result, $provider->authorise($card, $money));
    }

    public function testCaptureCallsCaptor(): void
    {
        $authoriser = $this->createAuthorisationMock();
        $captor = $this->createCaptorMock();
        $refunder = $this->createRefundMock();
        $token = $this->createTokenMock();
        $result = $this->createResultMock();

        $captor
            ->expects($this->once())
            ->method('capture')
            ->with($this->isInstanceOf(ValidatingProvider::class), $token)
            ->willReturn($result);

        $provider = new ValidatingProvider('valid-identifier', $authoriser, $captor, $refunder);

        $this->assertSame($result, $provider->capture($token));
    }

    public function testRefundCallsRefunder(): void
    {
        $authoriser = $this->createAuthorisationMock();
        $captor = $this->createCaptorMock();
        $refunder = $this->createRefundMock();
        $transaction = $this->createTransactionMock();
        $money = $this->createMoneyFake();
        $result = $this->createResultMock();

        $refunder
            ->expects($this->once())
            ->method('refund')
            ->with($this->isInstanceOf(ValidatingProvider::class), $transaction, $money)
            ->willReturn($result);

        $provider = new ValidatingProvider('valid-identifier', $authoriser, $captor, $refunder);

        $this->assertSame($result, $provider->refund($transaction, $money));
    }

    private function createAuthorisationMock(): AuthorisationInterface & MockObject
    {
        return $this->createMock(AuthorisationInterface::class);
    }

    private function createCaptorMock(): CaptureInterface & MockObject
    {
        return $this->createMock(CaptureInterface::class);
    }

    private function createRefundMock(): RefundInterface & MockObject
    {
        return $this->createMock(RefundInterface::class);
    }

    private function createCardMock(): CardInterface & MockObject
    {
        return $this->createMock(CardInterface::class);
    }

    private function createMoneyFake(): Money
    {
        return Money::of((string)\random_int(1, 999), Currency::of('EUR'));
    }

    private function createTokenMock(): TokenInterface & MockObject
    {
        return $this->createMock(TokenInterface::class);
    }

    private function createTransactionMock(): TransactionInterface & MockObject
    {
        return $this->createMock(TransactionInterface::class);
    }

    private function createResultMock(): ResultInterface & MockObject
    {
        return $this->createMock(ResultInterface::class);
    }
}
