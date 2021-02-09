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

use BN\BN;
use CBOR\ByteStringObject;
use InvalidArgumentException;
use Techworker\RadixDLT\Crypto\Keys\AbstractKeyPair;
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
 * Class RadixString
 */
#[Json(prefix: ':u20:', encoding: 'bin')]
#[CBOR(prefix: 4, target: ByteStringObject::class)]
#[Encoding(encoding: 'bin')]
class UInt256 extends BytesBased
{
    protected BN $bn;

    /**
     * RadixUInt256 constructor.
     * @param int[] $bytes
     * @throws \Exception
     */
    public function __construct(array $bytes)
    {
        parent::__construct($bytes);
        $this->bn = new BN($bytes);
    }

    /**
     * @param array|string $json
     * @return static
     * @throws \Exception
     */
    public static function fromJson(array|string $json): static
    {
        if(is_array($json)) {
            throw new \Exception('...');
        }

        $jsonPrefix = static::getJsonPrefix();
        if($jsonPrefix === null) {
            throw new \Exception('...');
        }

        $withoutPrefix = substr($json, strlen($jsonPrefix));
        /** @var int[] $bnBytes */
        $bnBytes = (new BN("$withoutPrefix"))->toArray();
        return new static($bnBytes);
    }

    /**
     * @return string|array
     * @throws \Exception
     */
    public function toJson(): string|array
    {
        $prefix = static::getJsonPrefix();
        if($prefix === null) {
            throw new \Exception('...');
        }

        return $prefix . (string)$this->bn->toString();
    }

    public function getBn() : BN {
        return $this->bn;
    }
}
