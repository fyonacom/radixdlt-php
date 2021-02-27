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
use Techworker\RadixDLT\Serialization\Attributes\Dson;
use Techworker\RadixDLT\Serialization\Attributes\Encoding;
use Techworker\RadixDLT\Serialization\Attributes\JsonPrimitive;
use Techworker\RadixDLT\Serialization\EncodingType;
use Techworker\RadixDLT\Types\Primitive;

/**
 * Class UInt256
 * @package Techworker\RadixDLT\Types\Primitives
 */
#[Dson(majorType: 2, prefix: 5)]
#[JsonPrimitive(prefix: ':u20:')]
#[Encoding(encoding: EncodingType::UINT256)]
class UInt256 extends Primitive
{
    protected BN $bn;

    /**
     * UInt256 constructor.
     * @param int[] $bytes
     * @throws \Exception
     */
    public function __construct(array $bytes)
    {
        if (count($bytes) !== 32) {
            throw new \InvalidArgumentException('Invalid uint32 length');
        }

        parent::__construct($bytes);
        $this->bn = new BN($bytes);
    }

    public function __toString(): string
    {
        return (string) $this->bn->toString();
    }

    public function getBN(): BN
    {
        return $this->bn;
    }
}
