<?php

declare(strict_types=1);

/*
 * This file is part of the RADIXDLT PHP package.
 *
 * (c) Copyright >=2020 Benjamin Ansbach & fyona.com <ben@fyona.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Techworker\RadixDLT\Crypto\Keys;

use BN\BN;
use Elliptic\EC\KeyPair;
use function Techworker\RadixDLT\ec;
use function Techworker\RadixDLT\encToBytes;

/**
 * Class AbstractEllipticEcBasedKeyPair
 *
 * A key pair class that uses simplito/elliptic-php.
 */
abstract class AbstractSimplitoEllipticKeyPair extends AbstractKeyPair
{
    /**
     * @inheritDoc
     */
    public static function generateNew(): static
    {
        /** @var KeyPair */
        $libKeyPair = ec(static::getName())->genKeyPair();
        return self::libKeyPairToKeyPair($libKeyPair);
    }

    /**
     * Helper function that can convert a simplito keypair to our keypair.
     *
     * @param KeyPair $kp
     * @return static
     */
    protected static function libKeyPairToKeyPair(KeyPair $kp): static
    {
        // TODO: send an array
        /** @var string $libPublicKey */
        $libPublicKey = $kp->getPublic(true, 'hex');
        $publicKey = PublicKey::fromHex($libPublicKey);

        $privateKey = null;
        /** @var BN|null $ecPrivateKey */
        $ecPrivateKey = $kp->getPrivate();
        if($ecPrivateKey !== null) {
            /** @var string $libPrivateKey */
            $libPrivateKey = $ecPrivateKey->toString(16, 2);
            $privateKey = PrivateKey::fromHex($libPrivateKey);
        }

        return new static($publicKey, $privateKey);
    }

    /**
     * @inheritDoc
     */
    public static function fromPublicKey(string|array|PublicKey $publicKey, ?string $enc = 'hex'): static
    {
        $bytes = [];
        if (is_string($publicKey)) {
            $bytes = encToBytes($publicKey, $enc);
        } elseif (is_array($publicKey)) {
            $bytes = $publicKey;
        } else {
            $bytes = $publicKey->toBytes();
        }

        /** @var KeyPair $libKeyPair */
        $libKeyPair = ec(static::getName())->keyFromPublic($bytes);
        return self::libKeyPairToKeyPair($libKeyPair);
    }

    /**
     * @inheritDoc
     */
    public static function fromPrivateKey(string|array|PrivateKey $privateKey, ?string $enc = 'hex'): static
    {
        $bytes = [];
        if (is_string($privateKey)) {
            $bytes = encToBytes($privateKey, $enc);
        } elseif (is_array($privateKey)) {
            $bytes = $privateKey;
        } else {
            $bytes = $privateKey->toBytes();
        }

        /** @var KeyPair $libKeyPair */
        $libKeyPair = ec(static::getName())->keyFromPrivate($bytes);
        return self::libKeyPairToKeyPair($libKeyPair);
    }
}
