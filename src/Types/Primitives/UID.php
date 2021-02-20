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

use BN\BN;
use CBOR\AbstractCBORObject;
use CBOR\ByteStringObject;
use Techworker\RadixDLT\Serialization\Interfaces\FromDsonInterface;
use Techworker\RadixDLT\Serialization\Interfaces\FromJsonInterface;
use Techworker\RadixDLT\Serialization\Interfaces\ToDsonInterface;
use Techworker\RadixDLT\Serialization\Interfaces\ToJsonInterface;
use Techworker\RadixDLT\Serialization\Serializer;
use Techworker\RadixDLT\Types\BytesBasedObject;

/**
 * Class EUID
 * @package Techworker\RadixDLT\Types\Primitives
 */
class UID extends BytesBasedObject implements
    FromJsonInterface,
    ToJsonInterface,
    FromDsonInterface,
    ToDsonInterface
{
    protected BN $shard;

    /**
     * EUID constructor.
     * @param int[] $bytes
     */
    public function __construct(array $bytes)
    {
        if(count($bytes) !== 16) {
            throw new \InvalidArgumentException('Bad length');
        }

        parent::__construct($bytes);
        $shard = array_slice($this->bytes, 0, 8);
        $this->shard = new BN(bytesToHex($shard), 16);
    }

    public function __toString(): string
    {
        return $this->toHex();
    }

    /**
     * Gets the shard.
     *
     * @return int[]|string
     */
    public function getShard(): BN
    {
        return $this->shard;
    }

    public static function fromJson(array | string $json): static
    {
        return new static(hexToBytes(
                              Serializer::primitiveFromJson($json, ':uid:')
                          ));
    }

    public function toJson(): string | array
    {
        return Serializer::primitiveToJson($this, ':uid:');
    }

    public static function fromDson(array | string | AbstractCBORObject $dson): static
    {
        return new static(
            Serializer::primitiveFromDson($dson, 2)
        );
    }

    public function toDson(): ByteStringObject
    {
        return new ByteStringObject(
            Serializer::primitiveToDson($this->bytes, 2)
        );
    }
}
