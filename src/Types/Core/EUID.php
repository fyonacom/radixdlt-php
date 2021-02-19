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
use Techworker\RadixDLT\Serialization\Interfaces\FromDsonInterface;
use Techworker\RadixDLT\Serialization\Interfaces\FromJsonInterface;
use Techworker\RadixDLT\Serialization\Serializer;
use Techworker\RadixDLT\Serialization\Interfaces\ToDsonInterface;
use Techworker\RadixDLT\Serialization\Interfaces\ToJsonInterface;
use Techworker\RadixDLT\Types\BytesBasedObject;

/**
 * Class EUID
 * @package Techworker\RadixDLT\Types\Core
 */
class EUID extends BytesBasedObject implements
    FromJsonInterface,
    ToJsonInterface,
    FromDsonInterface,
    ToDsonInterface
{
    /**
     * Shard bytes (first 8 bytes)
     *
     * @var int[]
     */
    protected array $shard;

    /**
     * EUID constructor.
     * @param int[] $bytes
     */
    public function __construct(array $bytes)
    {
        parent::__construct($bytes);
        $this->shard = array_slice($this->bytes, 0, 8);
    }

    public function __toString(): string
    {
        return $this->toHex();
    }

    public static function getDefaultEncoding(): string
    {
        return 'hex';
    }

    /**
     * Gets the shard.
     *
     * @return int[]|string
     */
    public function getShard(string $enc = null): array | string
    {
        return bytesToEnc($this->shard, $enc ?? 'hex');
    }

    /**
     * Creates a new UID instance from the given number
     * @return EUID
     * @throws \Exception
     */
    public static function fromInt(int $number): self
    {
        $bytes = array_fill(0, 16, 0);
        writeUInt32BE($bytes, $number, 12);

        return parent::from($bytes);
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
