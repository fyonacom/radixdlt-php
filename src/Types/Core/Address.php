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

use CBOR\ByteStringObject;
use InvalidArgumentException;
use Techworker\RadixDLT\Crypto\Keys\AbstractKeyPair;
use Techworker\RadixDLT\Crypto\Keys\CurveInterface;
use Techworker\RadixDLT\Crypto\Keys\CurveResolver;
use Techworker\RadixDLT\Crypto\Keys\Curves\Secp256k1;
use Techworker\RadixDLT\Crypto\Keys\PrivateKey;
use Techworker\RadixDLT\Crypto\Keys\PublicKey;
use Techworker\RadixDLT\Serialization\Attributes\CBOR;
use Techworker\RadixDLT\Serialization\Attributes\Encoding;
use Techworker\RadixDLT\Serialization\Attributes\Json;
use Techworker\RadixDLT\Types\BytesBased;
use function Techworker\RadixDLT\bytesToEnc;
use function Techworker\RadixDLT\encToBytes;
use function Techworker\RadixDLT\radixHash;

/**
 * Class RadixAddress
 *
 * Represents a radix address.
 */
#[Json(prefix: ':adr:', encoding: 'base58')]
#[CBOR(prefix: 4, target: ByteStringObject::class)]
#[Encoding(encoding: 'base58')]
class Address extends BytesBased
{
    /** @var int[] */
    protected array $hash;
    protected EUID $uid;
    protected AbstractKeyPair $keyPair;

    /**
     * Address constructor.
     *
     * @param int[] $bytes
     * @param AbstractKeyPair|null $keyPair
     */
    protected function __construct(array $bytes, AbstractKeyPair $keyPair = null)
    {
        parent::__construct($bytes);

        // when there is no keypair given, we will, at least,
        // extract the public key and build a half empty keypair
        // with it.
        if($keyPair === null) {
            $publicKey = array_slice($this->bytes, 1, count($this->bytes) - 5);
            $curve = CurveResolver::curveByPublicKeyLength(count($publicKey));

            /** @var AbstractKeyPair $curve */
            $keyPair = $curve::fromPublicKey($publicKey);
            $this->keyPair = $keyPair;
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
            if (!isset($checksum[$i]) || $checksum[$i] !== $this->bytes[count($this->bytes) - 4 + $i]) {
                throw new InvalidArgumentException('Invalid address');
            }
        }

        $this->hash = radixHash($this->getPublicKey()->toBytes());
        $this->uid = EUID::from(array_slice($this->hash, 0, 16));
    }

    /**
     * Generates a new address with the given curve type.
     *
     * @param string $curve
     * @param int $universe
     * @return Address
     */
    public static function generate(string $curve = Secp256k1::class, int $universe = 0): Address
    {
        /** @var AbstractKeyPair $curve */
        return self::fromKeyPair($curve::generateNew(), $universe);
    }

    /**
     * Creates a new radix address instance from the given keypair.
     *
     * @param AbstractKeyPair $keyPair
     * @param int $universe
     * @return Address
     */
    public static function fromKeyPair(AbstractKeyPair $keyPair, int $universe = 0) : Address {
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
     * @param array|string|PublicKey $publicKey
     * @param string|null $enc
     * @param int $universe
     * @return Address
     * @throws InvalidArgumentException
     */
    public static function fromPublicKey(array|string|PublicKey $publicKey, string $enc = null, int $universe = 0): Address
    {
        $length = 0;
        if(is_string($publicKey)) {
            $length = count(encToBytes($publicKey, $enc));
        } elseif(is_array($publicKey)) {
            $length = count($publicKey);
        }

        if($publicKey instanceof PublicKey) {
            $curve = $publicKey->getCurve();
        } else {
            $curve = CurveResolver::curveByPublicKeyLength($length);
        }
        /** @var AbstractKeyPair $curve */
        return self::fromKeyPair($curve::fromPublicKey($publicKey, $enc), $universe);
    }

    /**
     * Derives an address from the given private key.
     *
     * @param array|string|PrivateKey $privateKey
     * @param string|null $enc
     * @param int $universe
     * @return Address
     */
    public static function fromPrivateKey(array|string|PrivateKey $privateKey, ?string $enc = 'hex', int $universe = 0): Address
    {
        $length = 0;
        if(is_string($privateKey)) {
            $length = count(encToBytes($privateKey, $enc));
        } elseif(is_array($privateKey)) {
            $length = count($privateKey);
        }

        if($privateKey instanceof PrivateKey) {
            $curve = $privateKey->getCurve();
        } else {
            $curve = CurveResolver::curveByPrivateKeyLength($length);
        }

        /** @var AbstractKeyPair $curve */
        return self::fromKeyPair($curve::fromPrivateKey($privateKey, $enc), $universe);
    }

    /**
     * Gets the universe identifier.
     *
     * @return int
     */
    public function getUniverse(): int
    {
        return $this->bytes[0];
    }

    /**
     * Gets the hash of the corresponding public key.
     *
     * @param string|null $enc
     * @return int[]|string
     */
    public function getHash(string $enc = null): array|string
    {
        return bytesToEnc($this->hash, $enc ?? 'hex');
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
     * @return EUID
     */
    public function getUID(): EUID
    {
        return $this->uid;
    }
}
