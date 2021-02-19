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
use Techworker\RadixDLT\Serialization\Interfaces\FromDsonInterface;
use Techworker\RadixDLT\Serialization\Interfaces\FromJsonInterface;
use Techworker\RadixDLT\Serialization\Serializer;
use Techworker\RadixDLT\Serialization\Interfaces\ToDsonInterface;
use Techworker\RadixDLT\Serialization\Interfaces\ToJsonInterface;
use Techworker\RadixDLT\Types\BytesBasedObject;

/**
 * Class AID
 * @package Techworker\RadixDLT\Types\Core
 */
class AID extends BytesBasedObject implements
    FromJsonInterface,
    ToJsonInterface,
    FromDsonInterface,
    ToDsonInterface
{
    public const BYTES = 32;

    /**
     * AID constructor.
     *
     * @param int[] $bytes
     */
    public function __construct(array $bytes)
    {
        parent::__construct($bytes);

        if (count($bytes) !== self::BYTES) {
            throw new InvalidArgumentException(
                'AID length !== ' . self::BYTES . ', is ' . count($bytes)
            );
        }
    }

    public function __toString(): string
    {
        return $this->toHex();
    }

    public static function fromJson(array | string $json): static
    {
        return new static(hexToBytes(
            Serializer::primitiveFromJson($json, ':aid:')
        ));
    }

    public function toJson(): string | array
    {
        return Serializer::primitiveToJson($this, ':aid:');
    }

    public static function fromDson(array | string | AbstractCBORObject $dson): static
    {
        return new static(
            Serializer::primitiveFromDson($dson, 8)
        );
    }

    public function toDson(): ByteStringObject
    {
        return new ByteStringObject(
            Serializer::primitiveToDson($this->bytes, 8)
        );
    }
}
