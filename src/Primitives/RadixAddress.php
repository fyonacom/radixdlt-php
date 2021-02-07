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

namespace Techworker\RadixDLT\Primitives;

use CBOR\ByteStringObject;
use InvalidArgumentException;
use ReflectionException;
use Techworker\RadixDLT\Crypto\Keys\AbstractKeyPair;
use Techworker\RadixDLT\Crypto\Keys\CurveResolver;
use Techworker\RadixDLT\Crypto\Keys\Curves\Secp256k1;
use Techworker\RadixDLT\Crypto\Keys\PrivateKey;
use Techworker\RadixDLT\Crypto\Keys\PublicKey;
use Techworker\RadixDLT\Serialization\Attributes\CBOR;
use Techworker\RadixDLT\Serialization\Attributes\DefaultEncoding;
use Techworker\RadixDLT\Serialization\Attributes\JsonPrefix;
use function Techworker\RadixDLT\bytesToBs58;
use function Techworker\RadixDLT\bytesToEnc;
use function Techworker\RadixDLT\encToBytes;
use function Techworker\RadixDLT\radixHash;

/**
 * Class RadixAddress
 */
#[JsonPrefix(prefix: ':adr:')]
#[CBOR(prefix: 4, target: ByteStringObject::class)]
#[DefaultEncoding(encoding: 'base58')]
class RadixAddress extends AbstractPrimitive
{
    protected int $universe = 0;
    protected AbstractKeyPair $keyPair;

    /**
     * Generates a new address with the given curve type.
     *
     * @param string $curve
     * @return RadixAddress
     */
    public static function generateNew(string $curve = Secp256k1::class): RadixAddress
    {
        /** @var $curve AbstractKeyPair fake.. */
        return self::fromKeyPair($curve::generateNew());
    }

    /**
     * Creates a new radix address instance from the given keypair.
     *
     * @param AbstractKeyPair $keyPair
     * @param int $universe
     * @return RadixAddress
     */
    public static function fromKeyPair(AbstractKeyPair $keyPair, int $universe = 0) : RadixAddress {
        $publicKey = $keyPair->getPublicKey()->to('bytes');
        $bytes = [];
        $bytes[0] = $universe;
        for ($i = 0; $i < count($publicKey); $i++) {
            $bytes[$i + 1] = $publicKey[$i];
        }

        $check = radixHash($bytes, 0, count($publicKey) + 1);
        for ($i = 0; $i < 4; $i++) {
            $bytes[count($publicKey) + 1 + $i] = $check[$i];
        }

        $address = new self($bytes);
        $address->universe = $universe;
        $address->keyPair = $keyPair;
        return $address;
    }

    /**
     * Initializes a RadixAddress from the given address string or byte array.
     *
     * @param string|array $data
     * @param string|null $enc
     * @return RadixAddress
     */
    public static function from(string|array $data, string $enc = null): RadixAddress
    {
        if(is_string($data)) {
            if($enc === 'json') {
                // strip the json prefix
                $raw = self::stripJsonPrefix($data);
            } elseif($enc === null) {
                $enc = self::defaultEncoding();
            }
            $raw = encToBytes($data, $enc);
        } else {
            $raw = $data;
        }

        $checksum = radixHash(
            array_slice($raw, 0, count($raw) - 4),
            0,
            count($raw) - 4
        );

        // check the checksum
        for ($i = 0; $i < 4; $i++) {
            if (!isset($checksum[$i]) || $checksum[$i] !== $raw[count($raw) - 4 + $i]) {
                throw new InvalidArgumentException('Invalid address');
            }
        }


        // extract the public key
        $publicKey = array_slice($raw, 1, count($raw) - 5);

        /** @var AbstractKeyPair $curve */
        $curve = CurveResolver::curveByPublicKeyLength(count($publicKey));

        return self::fromKeyPair($curve::fromPublicKey($publicKey), $raw[0]);
    }

    /**
     * Derives an address from the given public key.
     *
     * @param array|string|PublicKey $publicKey
     * @param string|null $enc
     * @param int $universe
     * @return RadixAddress
     * @throws InvalidArgumentException
     */
    public static function fromPublicKey(array|string|PublicKey $publicKey, string $enc = null, int $universe = 0): RadixAddress
    {
        $length = 0;
        if(is_string($publicKey)) {
            $length = count(encToBytes($publicKey, $enc));
        } elseif(is_array($publicKey)) {
            $length = count($publicKey);
        }

        /** @var AbstractKeyPair $curve */
        if($publicKey instanceof PublicKey) {
            $curve = $publicKey->getCurve();
        } else {
            $curve = CurveResolver::curveByPublicKeyLength($length);
        }

        return self::fromKeyPair($curve::fromPublicKey($publicKey, $enc), $universe);
    }

    /**
     * Derives an address from the given private key.
     *
     * @param array|string|PrivateKey $privateKey
     * @param string|null $enc
     * @param int $universe
     * @return RadixAddress
     */
    public static function fromPrivateKey(array|string|PrivateKey $privateKey, ?string $enc = 'hex', int $universe = 0): RadixAddress
    {
        $length = 0;
        if(is_string($privateKey)) {
            $length = count(encToBytes($privateKey, $enc));
        } elseif(is_array($privateKey)) {
            $length = count($privateKey);
        }

        /** @var AbstractKeyPair $curve */
        if($privateKey instanceof PrivateKey) {
            $curve = $privateKey->getCurve();
        } else {
            $curve = CurveResolver::curveByPrivateKeyLength($length);
        }

        return self::fromKeyPair($curve::fromPrivateKey($privateKey, $enc), $universe);
    }

    /**
     * Gets the universe identifier.
     *
     * @return int
     */
    public function getUniverse(): int
    {
        return $this->universe;
    }

    /**
     * Gets the hash of the corresponding public key.
     *
     * @param string|null $enc
     * @return array|string
     */
    public function getHash(?string $enc = 'bytes'): array|string
    {
        $hash = radixHash($this->getPublicKey()->to('bytes'));
        return $enc === 'bytes' ? $hash : bytesToEnc($hash, $enc);
    }

    /**
     * Gets the corresponding public key.
     *
     * @return PublicKey
     */
    public function getPublicKey(): PublicKey
    {
        return $this->keyPair->getPublicKey();
    }

    /**
     * Gets the corresponding private key (if available).
     *
     * @return PrivateKey|null
     */
    public function getPrivateKey(): ?PrivateKey
    {
        return $this->keyPair->getPrivateKey();
    }

    /**
     * Gets the related UID.
     *
     * @return RadixUID
     */
    public function getUID(): RadixUID
    {
        return new RadixUID(array_slice($this->getHash(), 0, 16));
    }

    /**
     * Gets the shard from the uid.
     *
     * @param string|null $enc
     * @return array|string
     */
    public function getShard(?string $enc = 'bytes'): array|string
    {
        return $this->getUID()->getShard($enc);
    }
}
