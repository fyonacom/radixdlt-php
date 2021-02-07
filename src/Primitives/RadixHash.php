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

use InvalidArgumentException;

/**
 * Class RadixHash
 *
 * @package Techworker\RadixDLT
 */
class RadixHash implements \Stringable, \JsonSerializable
{
    /**
     * Bytes of the UID.
     *
     * @var array
     */
    protected array $bytes;

    /**
     * RadixUID constructor.
     *
     * @param string|array $data
     */
    public function __construct(string|array $data, string $enc = 'hex')
    {
        $this->bytes = [];
        if(is_string($data)) {
            $this->bytes = encToBytes($data, $enc);
        } elseif(is_array($data)) {
            $this->bytes = $data;
        }

        if(count($this->bytes) !== 64) {
            throw new InvalidArgumentException('A hash must be 64 bytes long.');
        }
    }

    /**
     * Gets a value indicating whether the given UID equals the current.
     *
     * @param RadixHash|string|array $hash
     * @param string $enc
     * @return bool
     */
    public function equals(RadixHash|string|array $hash, string $enc = 'hex') {
        if($hash instanceof RadixHash) {
            return $hash->toString('hex') === $this->toString('hex');
        } elseif(is_string($hash)) {
            return $hash === $this->toString($enc);
        } elseif (is_array($hash)) {
            return bytesToHex($hash) === $this->toString();
        }
    }

    /**
     * @param string $enc
     * @return array|string
     */
    public function to(string $enc = 'hex') {
        return match ($enc) {
            'array' => $this->bytes,
            'json' => ':uid:' . $this->to('hex'),
            default => bytesToEnc($enc),
        };
    }

    /**
     * Gets the hex representation.
     *
     * @return string
     */
    public function __toString() : string
    {
        return $this->toString('hex');
    }

    /**
     * Gets the JSON representation of the UID.
     *
     * @return string
     */
    public function jsonSerialize() : string
    {
        return ':uid:' . (string)$this;
    }
}
