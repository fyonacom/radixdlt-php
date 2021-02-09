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

use CBOR\ByteStringObject;
use Techworker\RadixDLT\Serialization\Attributes\CBOR;
use Techworker\RadixDLT\Serialization\Attributes\Encoding;
use Techworker\RadixDLT\Serialization\Attributes\Json;
use Techworker\RadixDLT\Types\BytesBased;

/**
 * Class RadixHash
 *
 * @package Techworker\RadixDLT
 */
#[Json(prefix: ':hsh:', encoding: 'hex')]
#[CBOR(prefix: 3, target: ByteStringObject::class)]
#[Encoding(encoding: 'hex')]
class Hash extends BytesBased
{
    public const BYTES = 64;
    /**
     * RadixUID constructor.
     *
     * @param int[] $bytes
     */
    public function __construct(array $bytes)
    {
        parent::__construct($bytes);

        if (count($this->bytes) !== self::BYTES) {
            throw new \InvalidArgumentException(
                "Hash length !== " . self::BYTES . ", is " . count($bytes)
            );
        }
    }
}
