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
 * Class EUID
 * @package Techworker\RadixDLT\Types\Primitives
 */
#[Dson(majorType: 2, prefix: 2)]
#[JsonPrimitive(prefix: ':uid:')]
#[Encoding(encoding: EncodingType::HEX)]
class UID extends Primitive
{
    protected BN $shard;

    /**
     * EUID constructor.
     * @param int[] $bytes
     */
    public function __construct(array $bytes)
    {
        if (count($bytes) !== 16) {
            throw new \InvalidArgumentException('Bad length');
        }

        parent::__construct($bytes);
        $shard = $this->slice(0, 8);
        $this->shard = new BN(bytesToHex($shard), 16);
    }

    /**
     * Gets the shard.
     */
    public function getShard(): BN
    {
        return $this->shard;
    }
}
