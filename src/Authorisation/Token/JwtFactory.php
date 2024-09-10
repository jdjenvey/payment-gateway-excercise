<?php

declare(strict_types=1);

namespace Vestiaire\Authorisation\Token;

use Brick\Money\Money;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Vestiaire\Authorisation\Token\Exception\OpenSslKeyNotReadableException;
use Vestiaire\Card\CardInterface;
use Vestiaire\Payment\Provider\ProviderInterface;

final class JwtFactory implements FactoryInterface
{
    private const string ALGORITHM = 'RS256';

    private string $privateKey;
    private string $publicKey;

    /** @var array<string, TokenInterface> */
    private array $cache = [];

    public function __construct(
        private readonly string $privateKeyPath,
        private readonly string $publicKeyPath,
        private readonly string $domain,
    )
    {
        if (!\is_file($this->privateKeyPath)) {
            throw new Exception\OpenSslKeyNotFoundException($this->privateKeyPath);
        }

        if (!\is_readable($this->privateKeyPath)) {
            throw new Exception\OpenSslKeyNotReadableException($this->privateKeyPath);
        }

        if (!\is_file($this->publicKeyPath)) {
            throw new Exception\OpenSslKeyNotFoundException($this->publicKeyPath);
        }

        if (!\is_readable($this->publicKeyPath)) {
            throw new Exception\OpenSslKeyNotReadableException($this->publicKeyPath);
        }
    }

    public function createToken(
        ProviderInterface $provider,
        CardInterface $card,
        Money $amount,
    ): TokenInterface
    {
        $time = new \DateTimeImmutable();
        $expires = $time->add(new \DateInterval('PT1M'));

        $payload = [
            'exp' => $expires->getTimestamp(),
            'iss' => $this->domain,
            'aud' => $this->domain,
            'provider' => $provider->getIdentifier(),
            'card' => $card->cardNumber()->__toString(),
            'amount' => $amount->getAmount()->__toString(),
            'currency' => $amount->getCurrency()->__toString(),
        ];

        $encoded = JWT::encode(
            $payload,
            $this->getPrivateKey(),
            self::ALGORITHM
        );

        $token = new \Vestiaire\Authorisation\Token\Jwt(
            $encoded,
            $expires,
            $provider->getIdentifier(),
            $card->cardNumber()->__toString(),
            $amount->getAmount()->__toString(),
            $amount->getCurrency()->__toString(),
        );

        $this->cache[$encoded] = $token;

        return $token;
    }

    public function decodeToken(string $encoded): TokenInterface
    {
        if (\array_key_exists($encoded, $this->cache)) {
            return $this->cache[$encoded];
        }

        $decoded = JWT::decode(
            $encoded,
            new Key(
                $this->getPublicKey(),
                self::ALGORITHM
            )
        );

        $token = new \Vestiaire\Authorisation\Token\Jwt(
            $encoded,
            new \DateTimeImmutable(
                \sprintf('@%d', $decoded->exp)
            ),
            $decoded->provider,
            $decoded->card,
            $decoded->amount,
            $decoded->currency,
        );

        $this->cache[$encoded] = $token;

        return $token;
    }

    private function getPrivateKey(): string
    {
        if (isset($this->privateKey)) {
            return $this->privateKey;
        }
        
        return $this->privateKey = (string)\file_get_contents($this->privateKeyPath);
    }

    public function getPublicKey(): string
    {
        if (isset($this->publicKey)) {
            return $this->publicKey;
        }
        
        return $this->publicKey = (string)\file_get_contents($this->publicKeyPath);
    }
}
