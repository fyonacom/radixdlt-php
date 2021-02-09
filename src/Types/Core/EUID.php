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
use function Techworker\RadixDLT\bytesToEnc;
use function Techworker\RadixDLT\encToBytes;
use function Techworker\RadixDLT\writeUInt32BE;

/**
 * Class RadixUID
 *
 * @package Techworker\RadixDLT
 */
#[Json(prefix: ':uid:', encoding: 'hex')]
#[CBOR(prefix: 2, target: ByteStringObject::class)]
#[Encoding(encoding: 'hex')]
class EUID extends BytesBased
{
    /**
     * Shard bytes (first 8 of bytes)
     *
     * @var int[]
     */
    protected array $shard;

    /**
     * RadixUID constructor.
     * @param int[] $bytes
     */
    public function __construct(array $bytes)
    {
        parent::__construct($bytes);
        $this->shard = array_slice($this->bytes, 0, 8);
    }

    /**
     * Gets the shard.
     *
     * @param string|null $enc
     * @return int[]|string
     */
    public function getShard(string $enc = null) : array|string {
        return bytesToEnc($this->shard, $enc ?? 'hex');
    }

    /**
     * Creates a new UID instance from the given number
     * @param int $number
     * @return EUID
     */
    public static function fromInt(int $number) : self
    {
        $bytes = array_fill(0, 16, 0);
        writeUInt32BE($bytes, $number, 12);

        return parent::from($bytes);
    }
}
