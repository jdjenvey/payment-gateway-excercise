<?php

declare(strict_types=1);

namespace VestiaireTest\Unit\Authorisation\Token;

use Brick\Money\Currency;
use Brick\Money\Money;
use Firebase\JWT\JWT;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Vestiaire\Authorisation\Token\Exception\OpenSslKeyNotFoundException;
use Vestiaire\Authorisation\Token\Exception\OpenSslKeyNotReadableException;
use Vestiaire\Authorisation\Token\JwtFactory;
use Vestiaire\Card\CardInterface;
use Vestiaire\Card\Number\CardNumberInterface;
use Vestiaire\Payment\Provider\ProviderInterface;
use Vestiaire\Authorisation\Token\TokenInterface;

class JwtFactoryTest extends TestCase
{
    private string $domain = 'example.com';
    private vfsStreamDirectory $root;

    public function setUp(): void
    {
        $this->root = vfsStream::setup('keys');

        vfsStream::newFile('private.pem')
            ->at($this->root)
            ->setContent(
                \file_get_contents(__DIR__ . '/../../../../keys/private.pem')
            );

        vfsStream::newFile('public.pem')
            ->at($this->root)
            ->setContent(
                \file_get_contents(__DIR__ . '/../../../../keys/public.pem')
            );
    }

    public function testCannotUseMissingPrivateKey(): void
    {
        $this->expectException(OpenSslKeyNotFoundException::class);

        new JwtFactory(
            'vfs://keys/foo.pem',
            'vfs://keys/public.pem',
            $this->domain
        );
    }

    public function testCannotUseUnreadablePrivateKey(): void
    {
        $this->expectException(OpenSslKeyNotReadableException::class);

        \chmod(vfsStream::url('keys/private.pem'), 0200);

        $this->createFactory();
    }

    public function testCannotUseMissingPublicKey(): void
    {
        $this->expectException(OpenSslKeyNotFoundException::class);

        new JwtFactory(
            'vfs://keys/private.pem',
            'vfs://keys/bar.pem',
            $this->domain
        );
    }

    public function testCannotUseUnreadablePublicKey(): void
    {
        $this->expectException(OpenSslKeyNotReadableException::class);

        \chmod(vfsStream::url('keys/public.pem'), 0200);

        $this->createFactory();
    }

    public function testCanDecodeUncachedToken()
    {
        $provider = $this->createProviderMock('provider-id');
        $card = $this->createCardMock('4111111111111111');
        $amount = Money::of('123.45', Currency::of('USD'));

        $token = $this->createFactory()->createToken($provider, $card, $amount);

        $sut = $this->createFactory();
        $out = $sut->decodeToken($token->toString());

        $this->assertInstanceOf(TokenInterface::class, $out);
        $this->assertSame($token->getProviderIdentifier(), $out->getProviderIdentifier());
        $this->assertSame($token->getCardNumber(), $out->getCardNumber());
        $this->assertSame($token->getAmount(), $out->getAmount());
        $this->assertSame($token->getCurrency(), $out->getCurrency());
    }

    public function testEncodingRepeatedlyDoesNotRereadThePrivateKeyFile()
    {
        $file = 'vfs://keys/private.pem';

        $provider = $this->createProviderMock('provider-id');
        $card = $this->createCardMock('4111111111111111');
        $amount = Money::of('123.45', Currency::of('USD'));

        $factory = $this->createFactory();

        $factory->createToken($provider, $card, $amount);

        $atime = \stat($file)['atime'];

        $factory->createToken(
            $this->createProviderMock('provider2'),
            $this->createCardMock('4131114511116711'),
            $amount
        );

        $factory->createToken(
            $this->createProviderMock('provider2'),
            $this->createCardMock('4131114511116711'),
            $amount
        );

        $this->assertSame(
            $atime,
            \stat($file)['atime']
        );
    }

    public function testDecodingRepeatedlyDoesNotRereadThePublicKeyFile()
    {
        $file = 'vfs://keys/public.pem';

        $generate = \Closure::bind(function (): string {
            $token = $this->createFactory()->createToken(
                $this->createProviderMock('provider-id'),
                $this->createCardMock('1234567890123456'),
                Money::of((string)\random_int(1, 9999), Currency::of('EUR'))
            );

            return $token->toString();
        }, $this);

        $factory = $this->createFactory();

        $factory->decodeToken($generate());

        $atime = \stat($file)['atime'];

        $factory->decodeToken($generate());
        $factory->decodeToken($generate());
        $factory->decodeToken($generate());

        $this->assertSame(
            $atime,
            \stat($file)['atime']
        );
    }

    public function testCreateTokenGeneratesJwt(): void
    {
        $factory = $this->createFactory();
        $provider = $this->createProviderMock('provider-id');
        $card = $this->createCardMock('4111111111111111');
        $amount = Money::of('123.45', Currency::of('USD'));

        $token = $factory->createToken($provider, $card, $amount);

        $this->assertInstanceOf(TokenInterface::class, $token);
        $this->assertSame('provider-id', $token->getProviderIdentifier());
        $this->assertSame('4111111111111111', $token->getCardNumber());
        $this->assertSame('123.45', $token->getAmount());
        $this->assertSame('USD', $token->getCurrency());
    }

    public function testDecodeTokenReturnsValidToken(): void
    {
        $factory = $this->createFactory();
        $provider = $this->createProviderMock('provider-id');
        $card = $this->createCardMock('4111111111111111');
        $amount = Money::of('123.45', Currency::of('USD'));

        $token = $factory->createToken($provider, $card, $amount);
        $encoded = $token->toString();

        $decodedToken = $factory->decodeToken($encoded);

        $this->assertInstanceOf(TokenInterface::class, $decodedToken);
        $this->assertSame($token->toString(), $decodedToken->toString());
        $this->assertSame($token->getProviderIdentifier(), $decodedToken->getProviderIdentifier());
        $this->assertSame($token->getCardNumber(), $decodedToken->getCardNumber());
        $this->assertSame($token->getAmount(), $decodedToken->getAmount());
        $this->assertSame($token->getCurrency(), $decodedToken->getCurrency());
    }

    private function createFactory(): JwtFactory
    {
        return new JwtFactory(
            'vfs://keys/private.pem',
            'vfs://keys/public.pem',
            $this->domain
        );
    }

    private function createProviderMock(string $id): ProviderInterface & MockObject
    {
        $provider = $this->createMock(ProviderInterface::class);

        $provider
            ->method('getIdentifier')
            ->willReturn($id);

        return $provider;
    }

    private function createCardMock(string $number): CardInterface & MockObject
    {
        $card = $this->createMock(CardInterface::class);
        $cardNumber = $this->createMock(CardNumberInterface::class);

        $cardNumber
            ->method('__toString')
            ->willReturn($number);

        $card
            ->method('cardNumber')
            ->willReturn($cardNumber);

        return $card;
    }
}
