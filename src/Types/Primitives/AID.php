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

use InvalidArgumentException;
use Techworker\RadixDLT\Serialization\Attributes\Dson;
use Techworker\RadixDLT\Serialization\Attributes\Encoding;
use Techworker\RadixDLT\Serialization\Attributes\JsonPrimitive;
use Techworker\RadixDLT\Serialization\EncodingType;
use Techworker\RadixDLT\Types\BytesTrait;
use Techworker\RadixDLT\Types\Primitive;

/**
 * Class AID
 * @package Techworker\RadixDLT\Types\Primitives
 */
#[Dson(majorType: 2, prefix: 8, property: 'aid')]
#[JsonPrimitive(prefix: ':aid:', property: 'aid')]
#[Encoding(encoding: EncodingType::HEX)]

class AID extends Primitive
{
    use BytesTrait;

    public const BYTES = 32;

    /**
     * AID constructor.
     *
     * @param int[] $bytes
     */
    public function __construct(array $bytes)
    {
        parent::__construct($bytes);

        if ($this->countBytes() !== self::BYTES) {
            throw new InvalidArgumentException(
                'AID length !== ' . self::BYTES . ', is ' . $this->countBytes()
            );
        }
    }
}
