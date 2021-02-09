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
use function Techworker\RadixDLT\binaryToBytes;
use function Techworker\RadixDLT\bytesToEnc;

/**
 * Class RadixHash
 *
 * @package Techworker\RadixDLT
 */
#[Json(prefix: ':rri:', encoding: 'bin')]
#[CBOR(prefix: 3, target: ByteStringObject::class)]
#[Encoding(encoding: 'bin')]
class RRI extends BytesBased
{
    protected Address $address;
    protected string $name;

    /**
     * RadixUID constructor.
     *
     * @param int[] $bytes
     */
    public function __construct(array $bytes)
    {
        parent::__construct($bytes);
        $rriString = $this->toBinary();
        $parts = explode('/', ltrim($rriString, '/'));
        if (count($parts) !== 2) {
            throw new \InvalidArgumentException(
                'RRI must be of the format /:address/:unique'
            );
        }

        $this->address = Address::fromBase58($parts[0]);
        $this->name = $parts[1];
    }

    public function getAddress(): Address
    {
        return $this->address;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public static function fromAddressAndSymbol(Address $address, string $symbol) : RRI
    {
        return RRI::fromBinary(
            sprintf('/%s/%s', $address->toBinary(), $symbol->toBinary())
        );
    }
}
