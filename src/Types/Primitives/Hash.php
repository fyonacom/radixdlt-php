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
use Techworker\RadixDLT\Types\Primitive;

/**
 * Class Hash
 * @package Techworker\RadixDLT\Types\Primitives
 */
#[Dson(majorType: 2, prefix: 3)]
#[JsonPrimitive(prefix: ':hsh:')]
#[Encoding(encoding: EncodingType::HEX)]
class Hash extends Primitive
{
    public const BYTES = 32;

    /**
     * Hash constructor.
     *
     * @param int[] $bytes
     */
    public function __construct(array $bytes)
    {
        parent::__construct($bytes);

        if ($this->countBytes() !== self::BYTES) {
            throw new InvalidArgumentException(
                'Hash length !== ' . self::BYTES . ', is ' . count($bytes)
            );
        }
    }

    /**
     * @return int[]
     */
    public static function createHash(array $data, int $offset = 0, int $length = 0): self
    {
        if ($offset !== 0) {
            $data = array_slice($data, $offset, $length);
        }

        $last = bytesToBinary($data);
        for ($i = 0; $i < \Techworker\RadixDLT\Radix::RADIX_HASH_ROUNDS; $i++) {
            $context = hash_init(\Techworker\RadixDLT\Radix::RADIX_HASH_ALG);
            hash_update($context, $last);
            $last = hash_final($context, true);
        }

        return new self(binaryToBytes($last));
    }
}
