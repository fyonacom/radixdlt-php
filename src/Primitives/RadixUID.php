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

namespace Techworker\RadixDLT\Primitives;

use CBOR\ByteStringObject;
use Techworker\RadixDLT\Serialization\Attributes\CBOR;
use Techworker\RadixDLT\Serialization\Attributes\DefaultEncoding;
use Techworker\RadixDLT\Serialization\Attributes\JsonPrefix;
use function Techworker\RadixDLT\bytesToEnc;
use function Techworker\RadixDLT\encToBytes;
use function Techworker\RadixDLT\writeUInt32BE;

/**
 * Class RadixUID
 *
 * @package Techworker\RadixDLT
 */
#[JsonPrefix(prefix: ':uid:')]
#[CBOR(prefix: 2, target: ByteStringObject::class)]
#[DefaultEncoding(encoding: 'hex')]
class RadixUID extends AbstractPrimitive
{
    /**
     * Shard bytes (first 8 of bytes)
     *
     * @var array
     */
    protected array $shard;

    /**
     * RadixUID constructor.
     * @param array $bytes
     */
    public function __construct(array $bytes)
    {
        $this->shard = array_slice($bytes, 0, 8);
        parent::__construct($bytes);
    }


    /**
     * Gets the shard.
     *
     * @param string $enc
     * @return array|string
     */
    public function getShard(string $enc = 'bytes') : array|string {
        if($enc !== 'bytes') {
            return bytesToEnc($this->shard, $enc);
        }

        return $this->shard;
    }

    public static function from(int|string|array $data, ?string $enc = 'hex')
    {
        $bytes = $data;
        if(is_int($data)) {
            $bytes = array_fill(0, 16, 0);
            writeUInt32BE($bytes, $data, 12);
        } elseif(is_string($data)) {
            $bytes = encToBytes($data, $enc);
        }

        return new self($bytes);
    }

    /**
     * Gets the hex representation.
     *
     * @return string
     */
    public function __toString() : string
    {
        return $this->to();
    }
}
