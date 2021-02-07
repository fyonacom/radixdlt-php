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
use function Techworker\RadixDLT\bytesToHex;
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
     *
     * @param int|string|array $value
     * @param string|null $enc
     */
    public function __construct(int|string|array $value, ?string $enc = 'hex')
    {
        if(is_int($value)) {
            $bytes = array_fill(0, 16, 0);
            writeUInt32BE($bytes, $value, 12);
        } elseif(is_string($value)) {
            $bytes = encToBytes($value, $enc);
        } else {
            $bytes = $value;
        }

        $this->shard = array_slice($bytes, 0, 8);

        parent::__construct($bytes);
    }

    /**
     * Gets the shard.
     *
     * @param string $enc
     * @return array|string
     */
    public function getShard(string $enc = 'array') : array|string {
        if($enc !== 'array') {
            return bytesToEnc($this->shard, $enc);
        }

        return $this->shard;
    }

    /**
     * Gets a value indicating whether the given UID equals the current.
     *
     * @param RadixUID|string|array $uid
     * @return bool
     */
    public function equals(RadixUID|string|array $uid) {
        if($uid instanceof RadixUID) {
            return (string)$uid === (string)$this;
        } elseif(is_string($uid)) {
            return $uid === (string)$this;
        } elseif (is_array($uid)) {
            return bytesToHex($uid) === (string)$this;
        }
    }

    /**
     * Gets the hex representation.
     *
     * @return string
     */
    public function __toString() : string
    {
        return $this->to('hex');
    }
}
