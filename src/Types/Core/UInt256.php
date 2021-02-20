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

use BN\BN;
use CBOR\AbstractCBORObject;
use CBOR\ByteStringObject;
use Techworker\RadixDLT\Serialization\Interfaces\FromDsonInterface;
use Techworker\RadixDLT\Serialization\Interfaces\FromJsonInterface;
use Techworker\RadixDLT\Serialization\Interfaces\ToDsonInterface;
use Techworker\RadixDLT\Serialization\Interfaces\ToJsonInterface;
use Techworker\RadixDLT\Types\BytesBasedObject;

/**
 * Class UInt256
 * @package Techworker\RadixDLT\Types\Core
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
        parent::__construct($bytes);
        $this->bn = new BN($bytes);
    }

    public function __toString()
    {
        return 'ABC';
    }

    /**
     * @param array|string $json
     * @return static
     * @throws \Exception
     */
    public static function fromJson(array | string $json): static
    {
        return new static([1]);
    }

    /**
     * @return string|array
     * @throws \Exception
     */
    public function toJson(): string | array
    {
        return 'ABC';
    }

    public function getBn(): BN
    {
        return $this->bn;
    }

    public static function fromDson(array | string | AbstractCBORObject $dson): static
    {
        return new static([1, 2, 3]);
    }

    public function toDson(): ByteStringObject
    {
        return new ByteStringObject('ABC');
    }
}
