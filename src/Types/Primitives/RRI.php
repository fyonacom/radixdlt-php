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

use Techworker\RadixDLT\Serialization\Attributes\Dson;
use Techworker\RadixDLT\Serialization\Attributes\Encoding;
use Techworker\RadixDLT\Serialization\Attributes\JsonPrimitive;
use Techworker\RadixDLT\Serialization\EncodingType;
use Techworker\RadixDLT\Types\Primitive;


#[Dson(majorType: 2, prefix: 6)]
#[JsonPrimitive(prefix: ':rri:')]
#[Encoding(encoding: EncodingType::STR)]
class RRI extends Primitive
{
    protected Address $address;

    protected string $symbol;

    /**
     * RRI constructor.
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
        $this->symbol = $parts[1];
    }

    public function getAddress(): Address
    {
        return $this->address;
    }

    public function getSymbol(): string
    {
        return $this->symbol;
    }

    public static function fromAddressAndSymbol(Address $address, string $symbol): self
    {
        return self::fromBinary(
            sprintf('/%s/%s', (string) $address, $symbol)
        );
    }
}
