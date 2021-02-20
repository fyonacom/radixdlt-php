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
 * Class RRI
 * @package Techworker\RadixDLT\Types\Primitives
 */
class RRI extends BytesBasedObject implements
    FromJsonInterface,
    ToJsonInterface,
    FromDsonInterface,
    ToDsonInterface
{
    protected Address $address;

    protected string $symbol;

    /**
     * RRI constructor.
     *
     * @param int[] $bytes
     */
    public function __construct(array $bytes)
    {
        parent::__construct($bytes);
        $rriString = $this->toBinary();
        $parts = explode('/', ltrim($rriString, '/'));
        if (count($parts) !== 2) {
            throw new \InvalidArgumentException(
                'RRI must be of the format /:address/:unique'
            );
        }

        $this->address = Address::fromBase58($parts[0]);
        $this->symbol = $parts[1];
    }

    public function __toString(): string
    {
        return $this->toBinary();
    }

    public function getAddress(): Address
    {
        return $this->address;
    }

    public function getSymbol(): string
    {
        return $this->symbol;
    }

    public static function fromAddressAndSymbol(Address $address, string $symbol): self
    {
        return self::fromBinary(
            sprintf('/%s/%s', (string) $address, $symbol)
        );
    }

    public static function fromJson(array | string $json): static
    {
        return new static(binaryToBytes(
            Serializer::primitiveFromJson($json, ':rri:')
        ));
    }

    public function toJson(): string | array
    {
        return Serializer::primitiveToJson($this, ':rri:');
    }

    public static function fromDson(array | string | AbstractCBORObject $dson): static
    {
        return new static(
            Serializer::primitiveFromDson($dson, 6)
        );
    }

    public function toDson(): ByteStringObject
    {
        return new ByteStringObject(
            Serializer::primitiveToDson($this->bytes, 6)
        );
    }
}
