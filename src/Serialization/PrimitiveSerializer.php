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
use CBOR\Decoder;
use CBOR\OtherObject\OtherObjectManager;
use CBOR\StringStream;
use CBOR\Tag\TagObjectManager;
use CBOR\TextStringObject;
use Techworker\RadixDLT\Serialization\Attributes\Dson;
use Techworker\RadixDLT\Serialization\Attributes\Encoding;
use Techworker\RadixDLT\Serialization\Attributes\JsonPrimitive;
use Techworker\RadixDLT\Types\Primitive;
use Techworker\RadixDLT\Types\Primitives\UInt256;

class PrimitiveSerializer
{
    /**
     * Tries to convert a primitive to its JSON form.
     *
     * @return T
     *@throws \InvalidArgumentException
     *
     * @template T of Primitive
     * @psalm-param class-string<T> $targetType
     * @throws \LogicException
     */
    public function fromJson(string $json, string $targetType): Primitive
    {
        // TODO: target tyoe can be null and identified by the prefix
        /** @var JsonPrimitive|null $jsonAttr */
        $jsonAttr = JsonPrimitive::getClassAttribute($targetType);

        // we need a #[Json()] attribute
        if ($jsonAttr === null) {
            throw new \LogicException('Missing #[Json()] Attribute on class ' . $targetType);
        }

        // check prefix
        $prefix = substr($json, 0, strlen($jsonAttr->getPrefix()));
        if ($prefix !== $jsonAttr->getPrefix()) {
            throw new \InvalidArgumentException('Json Prefix does not equal expected prefix: ' . $prefix);
        }

        // strip the prefix from the value
        $strippedJson = substr($json, strlen($jsonAttr->getPrefix()));
        $encoding = $this->getEncoding($jsonAttr, $targetType);
        return $this->fromString($strippedJson, $targetType, $encoding);
    }

    /**
     * Tries to create the JSON form of a primitive.
     */
    public function toJson(Primitive $primitive): string
    {
        /** @var JsonPrimitive $jsonAttr */
        $jsonAttr = JsonPrimitive::getClassAttribute($primitive::class);
        return $jsonAttr->getPrefix() . $this->toString($primitive, $jsonAttr->getEncoding());
    }

    /**
     * Tries to create the JSON form of a primitive.
     */
    public function toString(Primitive $primitive, ?string $encoding = null): string
    {
        if ($encoding === null) {
            /** @var Encoding $encodingAttr */
            $encodingAttr = Encoding::getClassAttribute($primitive::class);
            $encoding = $encodingAttr->getEncoding();
        }

        return match ($encoding) {
            EncodingType::BASE58 => bytesToBase58($primitive->toBytes()),
            EncodingType::BASE64 => bytesToBase64($primitive->toBytes()),
            EncodingType::BIN => bytesToBinary($primitive->toBytes()),
            EncodingType::HEX => bytesToHex($primitive->toBytes()),
            /** @var UInt256 $primitive|Primitive */
            EncodingType::UINT256 => $primitive->getBN()->toString()
        };
    }

    /**
     * @template T of Primitive
     * @psalm-param class-string<T> $targetPrimitiveClass
     * @return T
     */
    public function fromString(string $value, string $targetPrimitiveClass, string $encoding = null): Primitive
    {
        if ($encoding === null) {
            /** @var Encoding $encodingAttr */
            $encodingAttr = Encoding::getClassAttribute($targetPrimitiveClass);
            $encoding = $encodingAttr->getEncoding();
        }

        return new $targetPrimitiveClass(
            match ($encoding) {
                EncodingType::BASE58 => base58ToBytes($value),
                EncodingType::BASE64 => base64ToBytes($value),
                EncodingType::BIN => binaryToBytes($value),
                EncodingType::HEX => hexToBytes($value),
                EncodingType::UINT256 => (new BN($value))->toArray('be', 32)
            }
        );
    }

    /**
     * @param array|string|CBORObject $dson
     * @throws \Exception
     *
     * @template T of Primitive
     * @psalm-param class-string<T> $targetPrimitiveClass
     * @return T
     */
    public function fromDson(array | string | CBORObject $dson, string $targetPrimitiveClass): Primitive
    {
        // TODO: $targetPrimitiveClass can be null and identified by the prefix
        /** @var Dson $dsonAttr */
        $dsonAttr = Dson::getClassAttribute($targetPrimitiveClass);

        // we'll convert it to binary representation
        if (is_array($dson)) {
            $dson = bytesToBinary($dson);
        }

        $cborInstance = null;
        if ($dson instanceof CBORObject) {
            $cborInstance = $dson;
        } else {
            $stream = new StringStream($dson);
            $decoder = new Decoder(new TagObjectManager(), new OtherObjectManager());
            $cborInstance = $decoder->decode($stream);
        }

        $dataBytes = binaryToBytes((string) $cborInstance->getNormalizedData());
        if ($dsonAttr->getPrefix() !== null) {
            $decodedPrefix = array_shift($dataBytes);
            if ($dsonAttr->getPrefix() !== $decodedPrefix) {
                throw new \Exception('Invalid dson target type');
            }
        }

        return new $targetPrimitiveClass($dataBytes);
    }

    /**
     * Returns a CBORObject instance from the given primitive.
     */
    public function toDson(Primitive $primitive): CBORObject
    {
        /** @var Dson $dsonAttr */
        $dsonAttr = Dson::getClassAttribute($primitive::class);

        $dsonSerializer = null;
        switch ($dsonAttr->getMajorType()) {
            case 1:
                $dsonSerializer = ByteStringObject::class;
                break;
            case 2:
                $prefix = $dsonAttr->getPrefix();
                if ($prefix !== null) {
                    return new ByteStringObject(
                        chr($prefix) .
                        $this->toString($primitive, EncodingType::BIN)
                    );
                }
                    return new ByteStringObject($this->toString($primitive));

                break;
            case 3:
                return new TextStringObject(
                    $this->toString($primitive, EncodingType::BIN)
                );
                break;
        }
    }

    protected function getEncoding(AttributeHasEncodingInterface $attribute, string $targetPrimitiveClass): ?string
    {
        // detect the encoding
        $encoding = null;
        if ($attribute->getEncoding() !== null) {
            $encoding = $attribute->getEncoding();
        } else {
            // if there is no encoding set, we'll check the direct encoding attribute
            /** @var Encoding|null $encodingAttr */
            $encodingAttr = Encoding::getClassAttribute($targetPrimitiveClass);
            if ($encodingAttr === null) {
                throw new \LogicException(
                    'Missing #[Encoding()] Attribute on class ' . $targetPrimitiveClass .
                    ' because the #[Json()] Attribute has no encoding set.'
                );
            }

            $encoding = $encodingAttr->getEncoding();
        }

        return $encoding;
    }
}
