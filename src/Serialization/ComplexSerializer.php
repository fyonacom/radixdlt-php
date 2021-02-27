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

namespace Techworker\RadixDLT\Serialization;

use BN\BN;
use CBOR\ByteStringObject;
use CBOR\CBORObject;
use CBOR\InfiniteListObject;
use CBOR\InfiniteMapObject;
use CBOR\MapItem;
use CBOR\OtherObject\FalseObject;
use CBOR\OtherObject\TrueObject;
use CBOR\TextStringObject;
use CBOR\UnsignedIntegerObject;
use Techworker\RadixDLT\Serialization\Attributes\DsonProperty;
use Techworker\RadixDLT\Serialization\Attributes\JsonProperty;
use Techworker\RadixDLT\Serialization\Attributes\Serializer;
use Techworker\RadixDLT\Types\Primitive;
use Techworker\RadixDLT\Types\Primitives\Address;
use Techworker\RadixDLT\Types\Primitives\Bytes;
use Techworker\RadixDLT\Types\Primitives\Hash;
use Techworker\RadixDLT\Types\Primitives\RRI;
use Techworker\RadixDLT\Types\Primitives\UID;
use Techworker\RadixDLT\Types\Primitives\UInt256;

/**
 * Class ComplexSerializer
 *
 * This serializer can serialize complex objects and arrays to either JSON
 * or DSON.
 */
class ComplexSerializer
{
    /**
     * ComplexSerializer constructor.
     *
     * @param PrimitiveSerializer $primitiveSerializer
     */
    public function __construct(
        protected PrimitiveSerializer $primitiveSerializer
    ) {
    }

    /**
     * Will create an object or array from the given JSON. In case of
     * an object, the target type can be automatically detected, as
     * long as there is a 'serializer' property in the root of the json.
     *
     * You can set the target type by hand, but in almost all cases the
     * serializer can detect the resulting object type.
     *
     * The target property is only used internally and can be ignored when
     * called from outside.
     *
     * @param mixed $json
     * @param string|null $targetType
     * @param JsonProperty|null $targetProperty
     * @return array|mixed|Primitive
     * @throws \Exception
     */
    public function fromJson(mixed $json, string $targetType = null, JsonProperty $targetProperty = null)
    {
        // it is most probably an object, we know it when there is a serializer property
        if (is_array($json)) {

            // its a sequential array or the target prop is an array
            if (array_keys($json) === range(0, count($json) - 1) ||
                ($targetProperty !== null && $targetProperty->getType() === 'array')) {
                $constructorParams = [];
                foreach ($json as $key => $item) {
                    $constructorParams[$key] = $this->fromJson(
                        $item, $targetProperty?->getArraySubType()
                    );
                }

                return $constructorParams;
            }

            // its an associative array where we can most likely detect the object
            // type

            // when there is a serializer present AND there is a config for
            // the serializer we will use this type. So it is possible to override
            // the instantiation via config
            /** @var string|null $serializer */
            $serializer = $json['serializer'] ?? null;
            $containerKey = 'serialization.' . (string)$serializer;

            // check if we can detect the target type from the container
            if ($serializer !== null && radix()->get($containerKey) !== null) {
                /** @var string|null $targetType */
                $targetType = radix()->get($containerKey);
            }

            // when it fails we cannot continue.
            if ($targetType === null) {
                throw new \Exception('Unable to map json to any objects');
            }

            // fetch the properties to deserialize
            $targetProperties = JsonProperty::getProperties($targetType);

            // collect the params that we want to send to the objects constructor
            $constructorParams = [];

            /** @var string $propertyName */
            /** @var JsonProperty $attribute */
            foreach ($targetProperties as $propertyName => $attribute) {

                // we will access the value either by the given json key or the name
                // of the property as fallback
                $accessKey = $attribute->getKey() ?? $propertyName;

                // TODO: reflect defaults and set them accordingly
                if (! isset($json[$accessKey])) {
                    throw new \Exception('property not in json: ' . $targetType . '::' . $accessKey);
                }

                // create the type
                $constructorParams[$attribute->getName()] = $this->fromJson(
                    $json[$accessKey],
                    $attribute->getType(),
                    $attribute
                );
            }

            // create the type
            return new $targetType(...$constructorParams);
        } elseif (is_a($targetType, Primitive::class, true)) {
            // its a primitive, so we can make use of the primitive serializer
            return $this->primitiveSerializer->fromJson($json, $targetType);
        } elseif (in_array($targetType, ['string', 'int', 'bool'], true)) {
            // its a builtin type, simply return the value
            return $json;
        }
    }

    /**
     * Tries to convert the given object to json.
     *
     * @return array|mixed|string
     */
    public function toJson(mixed $input)
    {
        // if the given input is an object
        if (is_object($input)) {

            // check if it's a primitive, in that case we will call
            // the primitive serializer and return the output
            if (is_a($input, Primitive::class)) {
                return $this->primitiveSerializer->toJson($input);
            }

            $sourceProperties = JsonProperty::getProperties($input);

            // TODO: Check if this is really faster than reflection
            $sourceData = (array) $input;
            $json = [];
            /** @var JsonProperty $attribute */
            /** @var string $propertyName */
            foreach ($sourceProperties as $propertyName => $attribute) {
                /** @var string $accessKey */
                $accessKey = $attribute->getKey() ?? $propertyName;
                $json[$accessKey] = $this->toJson(
                    $sourceData[chr(0) . '*' . chr(0) . $attribute->getName()]
                );
            }

            // fetch the serializer name and assign it if there is one defined
            $serializer = Serializer::getClassAttribute($input);
            if ($serializer !== null) {
                $json['serializer'] = $serializer->getName();
            }

            return $json;
        } elseif (is_array($input)) {
            // loop the array and convert its values
            $new = [];
            foreach ($input as $key => $value) {
                $new[$key] = $this->toJson($value);
            }
            return $new;
        } elseif (is_a($input, Primitive::class)) {
            // simply convert the primitive
            return $this->primitiveSerializer->toJson($input);
        }

        // return whatever is givenm, not sure yet if this is supposed
        // to happen at all
        return $input;
    }

    /**
     * Will create an object or array from the given DSON. In case of
     * an object, the target type can be automatically detected, as
     * long as there is a 'serializer' property in the root of the json.
     *
     * You can set the target type by hand, but in almost all cases the
     * serializer can detect the resulting object type.
     *
     * The target property is only used internally and can be ignored when
     * called from outside.
     *
     * @param CBORObject $dson
     * @param string|null $targetType
     * @param DsonProperty|null $targetProperty
     * @return mixed|Primitive|null
     * @throws \Exception
     */
    public function fromDson(CBORObject $dson, string $targetType = null, DsonProperty $targetProperty = null)
    {
        // recursively loop the dson
        switch (get_class($dson)) {
            case InfiniteMapObject::class:
                // create a better hashmap, the iterator does not give good results,
                // it's simply to improve readability
                $loopable = [];
                /** @var MapItem $mapItem */
                foreach ($dson->getIterator() as $mapItem) {
                    $loopable[$mapItem->getKey()->getNormalizedData()] = $mapItem->getValue();
                }

                // when there is a serializer present AND there is a config for
                // the serializer we will use this type. So it is possible to override
                // the instantiation via config
                $serializer = null;
                if(isset($loopable['serializer'])) {
                    $serializer = $loopable['serializer']->getNormalizedData();
                }
                $containerKey = 'serialization.' . (string)$serializer;
                if ($serializer !== null && radix()->get($containerKey) !== null) {
                    /** @var string|null $targetType */
                    $targetType = radix()->get($containerKey);
                }

               if ($targetType === null) {
                    throw new \Exception('Unable to map dson to any objects');
                }

                $targetProperties = DsonProperty::getProperties($targetType);
                $data = [];

                /**
                 * @var string $propertyName
                 * @var DsonProperty $attribute
                 */
                foreach ($targetProperties as $propertyName => $attribute) {
                    // TODO: reflect defaults and set them accordingly
                    if (! isset($loopable[$attribute->getKey()])) {
                        throw new \Exception('property not in dson: ' . $attribute->getKey());
                    }
                    $data[$attribute->getName()] = $this->fromDson(
                        $loopable[$attribute->getKey()],
                        $attribute->getType(),
                        $attribute
                    );
                }

                return new $targetType(...$data);
            case ByteStringObject::class:
                /** @var PrimitiveSerializer $serializer */
                $dataBytes = binaryToBytes((string) $dson->getNormalizedData());
                // check the prefix
                // TODO: I'm not sure if the responsibility of the prefix should lie here or
                // in the primitive serializer
                $decodedPrefix = array_shift($dataBytes);
                switch ($decodedPrefix) {
                    case 1:
                        return $this->primitiveSerializer->fromDson($dson, Bytes::class);
                    case 2:
                        return $this->primitiveSerializer->fromDson($dson, UID::class);
                    case 3:
                        return $this->primitiveSerializer->fromDson($dson, Hash::class);
                    case 4:
                        return $this->primitiveSerializer->fromDson($dson, Address::class);
                    case 5:
                        return $this->primitiveSerializer->fromDson($dson, UInt256::class);
                    case 6:
                        return $this->primitiveSerializer->fromDson($dson, RRI::class);
                    default:
                        throw new \Exception('Unknown prefix in prop: ' . $targetProperty->getName());
                }
                // no break
            case TextStringObject::class:
                return $dson->getNormalizedData();
            default:
                throw new \Exception('Unknown type: ' . $targetProperty->getName());
        }
    }

    /**
     * A method to transform an object to a DSON representation.
     *
     * @return array|CBORObject|InfiniteMapObject|FalseObject|TrueObject|TextStringObject|UnsignedIntegerObject|mixed|string
     * @throws \Exception
     */
    public function toDson(mixed $input)
    {
        if (is_object($input)) {
            // check if it's a primitive, in that case we will call the primitive serializer
            if (is_a($input, Primitive::class)) {
                /** @var Primitive $input */
                return \radix()->get(PrimitiveSerializer::class)->toDson($input);
            }

            $sourceProperties = DsonProperty::getProperties($input);
            $sourceData = (array) $input;
            $dson = new InfiniteMapObject();
            /** @var JsonProperty $sourceProperty */
            foreach ($sourceProperties as $propertyName => $attribute) {
                $accessKey = $attribute->getKey() ?? $propertyName;
                // TODO: isset check
                $dson->append(
                    new TextStringObject($accessKey),
                    $this->toDson($sourceData[chr(0) . '*' . chr(0) . $accessKey])
                );
            }

            // fetch the serializer name and assign it if there is one defined
            $serializer = Serializer::getClassAttribute($input);
            if ($serializer !== null) {
                $dson->append(
                    new TextStringObject('serializer'),
                    new TextStringObject($serializer->getName())
                );
            }
            return $dson;
        } elseif (is_array($input)) {
            $ret = new InfiniteListObject();
            foreach ($input as $item) {
                $ret->add($this->toDson($item));
            }
            return $ret;
        } elseif (is_a($input, Primitive::class)) {
            return \radix()->get(PrimitiveSerializer::class)->toJson($input);
        }
        if (is_string($input)) {
            return new TextStringObject($input);
        } elseif (is_int($input)) {
            $v = hexToString((string) (new BN((string) $input))->toString(16, 8));
            if ($v[0] === '-') {
                $unsigned = true;
            }

            return UnsignedIntegerObject::createObjectForValue(27, $v);
        } elseif (is_bool($input)) {
            if ($input === true) {
                return new TrueObject();
            }
            return new FalseObject();
        }
        return $input;
    }
}
