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
use Techworker\RadixDLT\Crypto\Keys\AbstractKeyPair;

/**
 * Class RadixHash
 *
 * @package Techworker\RadixDLT
 */
class RadixHash extends AbstractPrimitive
{
    /**
     * RadixUID constructor.
     *
     * @param array $bytes
     */
    public function __construct(array $bytes)
    {
        if(count($bytes) !== 64) {
            throw new InvalidArgumentException('A hash must be 64 bytes long.');
        }

        parent::__construct($bytes);
    }

    public static function from(string|array $data, string $enc = null) {
        if(is_string($data)) {
            $this->bytes = encToBytes($data, $enc);
        } elseif(is_array($data)) {
            $this->bytes = $data;
        }
    }

    /**
     * @param string $enc
     * @return array|string
     */
    public function to(string $enc = 'hex') {
        return match ($enc) {
            'bytes' => $this->bytes,
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
