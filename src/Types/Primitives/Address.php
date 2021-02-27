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

namespace Techworker\RadixDLT\Types\Primitives;

use InvalidArgumentException;
use Techworker\RadixDLT\Crypto\Keys\Curves\Secp256k1;
use Techworker\RadixDLT\Crypto\Keys\KeyPair;
use Techworker\RadixDLT\Crypto\Keys\PrivateKey;
use Techworker\RadixDLT\Crypto\Keys\PublicKey;
use Techworker\RadixDLT\Serialization\Attributes\Dson;
use Techworker\RadixDLT\Serialization\Attributes\Encoding;
use Techworker\RadixDLT\Serialization\Attributes\JsonPrimitive;
use Techworker\RadixDLT\Serialization\EncodingType;
use Techworker\RadixDLT\Types\Primitive;

#[Dson(majorType: 2, prefix: 4)]
#[JsonPrimitive(prefix: ':adr:')]
#[Encoding(encoding: EncodingType::BASE58)]
class Address extends Primitive
{
    protected Hash $hash;

    protected UID $uid;

    protected KeyPair $keyPair;

    /**
     * Address constructor.
     * @param int[] $bytes
     */
    public function __construct(array $bytes, KeyPair $keyPair = null)
    {
        parent::__construct($bytes);

        // when there is no keypair given, we will, at least,
        // extract the public key and build a half empty keypair
        // with it.
        if ($keyPair === null) {
            $publicKey = $this->slice(1, $this->countBytes() - 5);
            $this->keyPair = radix()->keyService()->fromPublicKey($publicKey);
        } else {
            $this->keyPair = $keyPair;
        }

        // validate the address
        $checksum = radixHash(
            $this->slice(0, $this->countBytes() - 4),
            0,
            $this->countBytes() - 4
        );

        for ($i = 0; $i < 4; $i++) {
            if (! $checksum->exists($i) || $checksum->at($i) !== $this->at($this->countBytes() - 4 + $i)) {
                throw new InvalidArgumentException('Invalid checksum for address');
            }
        }

        $this->hash = Hash::createHash($this->getPublicKey()->toBytes());
        $this->uid = new UID($this->hash->slice(0, 16));
    }

    /**
     * Gets the string representation of the primitive using the default encoding.
     */
    public function __toString(): string
    {
        return $this->toBase58();
    }

    /**
     * Generates a new address with the given curve type.
     */
    public static function generateNew(string $curve = Secp256k1::class, int $universe = -1): self
    {
        $radix = radix();
        if ($universe === -1) {
            $universe = $radix->connection()->getUniverseMagicByte();
        }

        return self::fromKeyPair($radix->keyService()->generateNew($curve), $universe);
    }

    /**
     * Creates a new radix address instance from the given keypair.
     */
    public static function fromKeyPair(KeyPair $keyPair, int $universe = -1): self
    {
        $radix = radix();
        if ($universe === -1) {
            $universe = $radix->connection()->getUniverseMagicByte();
        }

        $publicKey = $keyPair->getPublicKey()->toBytes();
        $bytes = [];
        $bytes[0] = $universe;
        for ($i = 0; $i < count($publicKey); $i++) {
            $bytes[$i + 1] = $publicKey[$i];
        }

        $check = radixHash($bytes, 0, count($publicKey) + 1);
        for ($i = 0; $i < 4; $i++) {
            $bytes[count($publicKey) + 1 + $i] = $check->at($i);
        }

        return new self($bytes, $keyPair);
    }

    /**
     * Derives an address from the given public key.
     *
     * @param int[]|string|PublicKey $publicKey
     * @return Address
     */
    public static function fromPublicKey(
        array | string | PublicKey $publicKey,
        int $universe = -1,
        string $enc = null
    ): self {
        $radix = radix();
        if ($universe === -1) {
            $universe = $radix->connection()->getUniverseMagicByte();
        }

        return self::fromKeyPair(
            radix()->keyService()->fromPublicKey($publicKey, $enc),
            $universe
        );
    }

    /**
     * Derives an address from the given private key.
     *
     * @param int[]|string|PrivateKey $privateKey
     */
    public static function fromPrivateKey(
        array | string | PrivateKey $privateKey,
        int $universe = 0,
        ?string $enc = 'hex',
    ): self {
        return self::fromKeyPair(
            radix()->keyService()->fromPrivateKey($privateKey, $enc),
            $universe
        );
    }

    /**
     * Gets the universe identifier.
     */
    public function getUniverseMagicByte(): int
    {
        return $this->at(0);
    }

    /**
     * Gets the hash of the corresponding public key.
     */
    public function getHash(): Hash
    {
        return $this->hash;
    }

    /**
     * Gets the corresponding public key.
     */
    public function getPublicKey(): PublicKey
    {
        return $this->keyPair->getPublicKey();
    }

    /**
     * Gets the corresponding private key (if available).
     */
    public function getPrivateKey(): ?PrivateKey
    {
        return $this->keyPair->getPrivateKey();
    }

    /**
     * Gets the related UID.
     */
    public function getUID(): UID
    {
        return $this->uid;
    }
}
