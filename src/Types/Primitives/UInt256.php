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
 * Class UInt256
 * @package Techworker\RadixDLT\Types\Primitives
 */
class UInt256 extends BytesBasedObject implements
    FromJsonInterface,
    ToJsonInterface,
    FromDsonInterface,
    ToDsonInterface
{
    protected BN $bn;

    /**
     * UInt256 constructor.
     * @throws \Exception
     */
    public function __construct(array $bytes)
    {
        if(count($bytes) !== 32) {
            throw new \InvalidArgumentException('Invalid uint32 length');
        }

        parent::__construct($bytes);
        $this->bn = new BN($bytes);
    }

    public function __toString() : string
    {
        return (string)$this->bn->toString();
    }

    /**
     * @param array|string $json
     * @return static
     * @throws \Exception
     */
    public static function fromJson(array | string $json): static
    {
        $bn = new BN(Serializer::primitiveFromJson($json, ':u20:'));
        return new static($bn->toArray('be', 32));
    }

    /**
     * @return string|array
     * @throws \Exception
     */
    public function toJson(): string | array
    {
        return Serializer::primitiveToJson($this, ':u20:');
    }

    public function getBn(): BN
    {
        return $this->bn;
    }

    public static function fromDson(array | string | AbstractCBORObject $dson): static
    {
        return new static(
            Serializer::primitiveFromDson($dson, 5)
        );
    }

    public function toDson(): ByteStringObject
    {
        return new ByteStringObject(
            Serializer::primitiveToDson($this->bytes, 5)
        );
    }
}
