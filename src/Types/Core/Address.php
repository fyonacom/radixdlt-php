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

namespace Techworker\RadixDLT\Types\Core;

use CBOR\AbstractCBORObject;
use CBOR\ByteStringObject;
use InvalidArgumentException;
use Techworker\RadixDLT\Crypto\Keys\Curves\Secp256k1;
use Techworker\RadixDLT\Crypto\Keys\KeyPair;
use Techworker\RadixDLT\Crypto\Keys\PrivateKey;
use Techworker\RadixDLT\Crypto\Keys\PublicKey;
use Techworker\RadixDLT\Serialization\Interfaces\FromDsonInterface;
use Techworker\RadixDLT\Serialization\Interfaces\FromJsonInterface;
use Techworker\RadixDLT\Serialization\Interfaces\ToDsonInterface;
use Techworker\RadixDLT\Serialization\Interfaces\ToJsonInterface;
use Techworker\RadixDLT\Serialization\Serializer;
use Techworker\RadixDLT\Types\BytesBasedObject;

class Address extends BytesBasedObject implements
    FromJsonInterface,
    ToJsonInterface,
    FromDsonInterface,
    ToDsonInterface
{
    /**
     * @var int[]
     */
    protected array $hash;

    protected EUID $uid;

    protected KeyPair $keyPair;

    /**
     * Address constructor.
     *
     * @param int[] $bytes
     */
    protected function __construct(array $bytes, KeyPair $keyPair = null)
    {
        parent::__construct($bytes);

        // when there is no keypair given, we will, at least,
        // extract the public key and build a half empty keypair
        // with it.
        if ($keyPair === null) {
            $publicKey = array_slice($this->bytes, 1, count($this->bytes) - 5);
            $this->keyPair = radix()->keyService()->fromPublicKey($publicKey);
        } else {
            $this->keyPair = $keyPair;
        }

        // validate the address
        $checksum = radixHash(
            array_slice($this->bytes, 0, count($this->bytes) - 4),
            0,
            count($this->bytes) - 4
        );

        for ($i = 0; $i < 4; $i++) {
            if (! isset($checksum[$i]) || $checksum[$i] !== $this->bytes[count($this->bytes) - 4 + $i]) {
                throw new InvalidArgumentException('Invalid checksum for address');
            }
        }

        $this->hash = radixHash($this->getPublicKey()->toBytes());
        $this->uid = EUID::fromBytes(array_slice($this->hash, 0, 16));
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
            $bytes[count($publicKey) + 1 + $i] = $check[$i];
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
        return $this->bytes[0];
    }

    /**
     * Gets the hash of the corresponding public key.
     *
     * @return int[]|string
     */
    public function getHash(string $enc = null): array | string
    {
        return bytesToEnc($this->hash, $enc ?? 'hex');
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
    public function getUID(): EUID
    {
        return $this->uid;
    }

    public static function fromJson(array | string $json): static
    {
        return new static(base58ToBytes(
            Serializer::primitiveFromJson($json, ':adr:')
        ));
    }

    public function toJson(): string | array
    {
        return Serializer::primitiveToJson($this, ':adr:');
    }

    public static function fromDson(array | string | AbstractCBORObject $dson): static
    {
        return new static(
            Serializer::primitiveFromDson($dson, 4)
        );
    }

    public function toDson(): ByteStringObject
    {
        return new ByteStringObject(
            Serializer::primitiveToDson($this->bytes, 4)
        );
    }
}
