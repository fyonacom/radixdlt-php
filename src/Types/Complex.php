<?php

namespace Techworker\RadixDLT\Types;

use CBOR\CBORObject;
use Techworker\RadixDLT\Serialization\ComplexSerializer;
use Techworker\RadixDLT\Serialization\Interfaces\FromDsonInterface;
use Techworker\RadixDLT\Serialization\Interfaces\FromJsonInterface;
use Techworker\RadixDLT\Serialization\Interfaces\ToDsonInterface;
use Techworker\RadixDLT\Serialization\Interfaces\ToJsonInterface;


abstract class Complex implements FromJsonInterface, ToJsonInterface, FromDsonInterface, ToDsonInterface
{
    /**
     * Gets a new primitive instance from the given data.
     *
     * @param array|string|CBORObject $dson
     * @return static
     * @throws \Exception
     */
    public static function fromDson(array | string | CBORObject $dson, string $enc = 'bin'): static
    {
        /** @var ComplexSerializer $serializer */
        $serializer = radix()->get(ComplexSerializer::class);
        return $serializer->fromDson($dson, static::class);
    }

    /**
     * Converts the primitive to a CBOR instance.
     */
    public function toDson(): CBORObject
    {
        /** @var ComplexSerializer $serializer */
        $serializer = radix()->get(ComplexSerializer::class);
        return $serializer->toDson($this);
    }

    /**
     * Creates a new primitive from the given JSON string.
     *
     * @param array|string $json
     * @return static
     * @throws \Exception
     */
    public static function fromJson(array | string $json): static
    {
        if (is_array($json)) {
            throw new \InvalidArgumentException('Invalid json value provided');
        }

        /** @var ComplexSerializer $serializer */
        $serializer = radix()->get(ComplexSerializer::class);
        return $serializer->fromJson($json, static::class);
    }

    /**
     * Gets the JSON representation of the current primitive.
     *
     * @return array|string
     */
    public function toJson(): array | string
    {
        /** @var ComplexSerializer $serializer */
        $serializer = radix()->get(ComplexSerializer::class);
        return $serializer->toJson($this);
    }
}
