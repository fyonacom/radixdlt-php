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

use CBOR\AbstractCBORObject;
use CBOR\ByteStringObject;
use Techworker\RadixDLT\Serialization\Interfaces\FromDsonInterface;
use Techworker\RadixDLT\Serialization\Interfaces\FromJsonInterface;
use Techworker\RadixDLT\Serialization\Interfaces\ToDsonInterface;
use Techworker\RadixDLT\Serialization\Interfaces\ToJsonInterface;
use Techworker\RadixDLT\Serialization\Serializer;
use Techworker\RadixDLT\Types\BytesBasedObject;

/**
 * Class Bytes
 * @package Techworker\RadixDLT\Types\Primitives
 */
class Bytes extends BytesBasedObject implements
    FromJsonInterface,
    ToJsonInterface,
    FromDsonInterface,
    ToDsonInterface
{
    public function __toString(): string
    {
        return $this->toBase64();
    }

    public static function fromJson(array | string $json): static
    {
        return new static(base64ToBytes(
            Serializer::primitiveFromJson($json, ':byt:')
        ));
    }

    public function toJson(): string | array
    {
        return Serializer::primitiveToJson($this, ':byt:');
    }

    public static function fromDson(array | string | AbstractCBORObject $dson): static
    {
        return new static(
            Serializer::primitiveFromDson($dson, 1)
        );
    }

    public function toDson(): ByteStringObject
    {
        return new ByteStringObject(
            Serializer::primitiveToDson($this->bytes, 1)
        );
    }
}
