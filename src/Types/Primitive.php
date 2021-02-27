<?php

namespace Techworker\RadixDLT\Types;

use CBOR\CBORObject;
use Techworker\RadixDLT\Serialization\Interfaces\FromDsonInterface;
use Techworker\RadixDLT\Serialization\Interfaces\FromJsonInterface;
use Techworker\RadixDLT\Serialization\Interfaces\ToDsonInterface;
use Techworker\RadixDLT\Serialization\Interfaces\ToJsonInterface;
use Techworker\RadixDLT\Serialization\PrimitiveSerializer;

/**
 * Class Primitive
 *
 * This abstract class acts as a base for all primitives and provides
 * the serialization possibilities.
 */
abstract class Primitive implements FromJsonInterface, ToJsonInterface, FromDsonInterface, ToDsonInterface
{
    use BytesTrait;

    /**
     * AbstractPrimitive constructor.
     *
     * @param int[] $bytes
     */
    public function __construct(array $bytes)
    {
        $this->bytes = [];
        $this->initBytes($bytes);
    }

    public function __toString(): string
    {
        /** @var PrimitiveSerializer $serializer */
        $serializer = radix()->get(PrimitiveSerializer::class);
        return $serializer->toString($this);
    }

    /**
     * Gets a new primitive instance from the given data.
     *
     * @param array|string|CBORObject $dson
     * @return static
     * @throws \Exception
     */
    public static function fromDson(array | string | CBORObject $dson, string $enc = 'bin'): static
    {
        /** @var PrimitiveSerializer $serializer */
        $serializer = radix()->get(PrimitiveSerializer::class);
        return $serializer->fromDson($dson, static::class);
    }

    /**
     * Converts the primitive to a CBOR instance.
     */
    public function toDson(): CBORObject
    {
        /** @var PrimitiveSerializer $serializer */
        $serializer = radix()->get(PrimitiveSerializer::class);
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

        /** @var PrimitiveSerializer $serializer */
        $serializer = radix()->get(PrimitiveSerializer::class);
        return $serializer->fromJson($json, static::class);
    }

    /**
     * Gets the JSON representation of the current primitive.
     *
     * @return array|string
     */
    public function toJson(): array | string
    {
        /** @var PrimitiveSerializer $serializer */
        $serializer = radix()->get(PrimitiveSerializer::class);
        return $serializer->toJson($this);
    }

    /**
     * @return static
     */
    public static function fromString(string $value, string $encoding = null): static
    {
        $serializer = radix()->get(PrimitiveSerializer::class);
        return $serializer->fromString($value, $encoding);
    }
}
