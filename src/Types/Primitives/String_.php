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
use CBOR\TextStringObject;
use Techworker\RadixDLT\Serialization\Interfaces\FromDsonInterface;
use Techworker\RadixDLT\Serialization\Interfaces\FromJsonInterface;
use Techworker\RadixDLT\Serialization\Interfaces\ToDsonInterface;
use Techworker\RadixDLT\Serialization\Interfaces\ToJsonInterface;
use Techworker\RadixDLT\Serialization\Serializer;
use Techworker\RadixDLT\Types\BytesBasedObject;

/**
 * Class String_
 * @package Techworker\RadixDLT\Types\Primitives
 */
class String_ extends BytesBasedObject implements
    FromJsonInterface,
    ToJsonInterface,
    FromDsonInterface,
    ToDsonInterface
{
    public function __toString(): string
    {
        return $this->toBinary();
    }

    public static function fromJson(array | string $json): static
    {
        return new static(binaryToBytes(
            Serializer::primitiveFromJson($json, ':str:')
        ));
    }

    public function toJson(): string | array
    {
        return Serializer::primitiveToJson($this, ':str:');
    }

    public static function fromDson(array | string | AbstractCBORObject $dson): static
    {
        return new static(
            Serializer::primitiveFromDson($dson, -1)
        );
    }

    public function toDson(): TextStringObject
    {
        return new TextStringObject(
            Serializer::primitiveToDson($this->bytes, -1)
        );
    }
}
