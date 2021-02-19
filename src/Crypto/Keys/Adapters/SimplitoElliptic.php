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

namespace Techworker\RadixDLT\Crypto\Keys\Adapters;

use BN\BN;
use Elliptic\EC;
use Techworker\RadixDLT\Crypto\Keys\AbstractCurve;
use Techworker\RadixDLT\Crypto\Keys\AdapterInterface;
use Techworker\RadixDLT\Crypto\Keys\CurveResolver;
use Techworker\RadixDLT\Crypto\Keys\KeyPair;
use Techworker\RadixDLT\Crypto\Keys\PrivateKey;
use Techworker\RadixDLT\Crypto\Keys\PublicKey;

/**
 * Class SimplitoElliptic
 *
 * A adapter using the userland library simplito/elliptic-php
 */
class SimplitoElliptic implements AdapterInterface
{
    /**
     * SimplitoElliptic constructor.
     * @param CurveResolver $curveResolver
     */
    public function __construct(
        protected CurveResolver $curveResolver
    ) {
    }

    public function generateNew(string $curve): KeyPair
    {
        // try to fetch the curve by the given name
        $curve = $this->curveResolver->byName($curve);

        /** @var AbstractCurve $curve */
        $ec = new EC($curve::getName());

        /** @var EC\KeyPair */
        $libKeyPair = $ec->genKeyPair();
        return self::libKeyPairToKeyPair($libKeyPair);
    }

    public function fromPublicKey(string | array | PublicKey $publicKey, ?string $enc = 'hex'): KeyPair
    {
        if (is_string($publicKey)) {
            $bytes = encToBytes($publicKey, $enc);
        } elseif (is_array($publicKey)) {
            $bytes = $publicKey;
        } else {
            $bytes = $publicKey->toBytes();
        }

        /** @var KeyPair $libKeyPair */
        return new KeyPair(PublicKey::fromBytes($bytes));
    }

    public function fromPrivateKey(string | array | PrivateKey $privateKey, ?string $enc = 'hex'): KeyPair
    {
        if (is_string($privateKey)) {
            $bytes = encToBytes($privateKey, $enc);
        } elseif (is_array($privateKey)) {
            $bytes = $privateKey;
        } else {
            $bytes = $privateKey->toBytes();
        }

        /** @var KeyPair $libKeyPair */
        // try to fetch the curve by the given name
        $curve = $this->curveResolver->byPrivateKeyLength(count($bytes));
        /** @var AbstractCurve $curve */
        $ec = new EC($curve::getName());

        /** @var EC\KeyPair */
        $libKeyPair = $ec->keyFromPrivate($bytes);
        return self::libKeyPairToKeyPair($libKeyPair);
    }

    /**
     * Helper function that can convert a simplito keypair to our keypair representation.
     */
    protected static function libKeyPairToKeyPair(EC\KeyPair $kp): KeyPair
    {
        // TODO: currently from BN to hex to array, try getPublic() to receive an uint8 directly
        /** @var string $libPublicKey */
        $libPublicKey = $kp->getPublic(true, 'hex');
        $publicKey = PublicKey::fromHex($libPublicKey);

        $privateKey = null;
        /** @var BN|null $ecPrivateKey */
        $ecPrivateKey = $kp->getPrivate();
        if ($ecPrivateKey !== null) {
            /** @var string $libPrivateKey */
            $libPrivateKey = $ecPrivateKey->toString(16, 2);
            $privateKey = PrivateKey::fromHex($libPrivateKey);
        }

        return new KeyPair($publicKey, $privateKey);
    }
}
