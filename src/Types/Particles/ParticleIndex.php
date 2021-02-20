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

namespace Techworker\RadixDLT\Types\Particles;

use Techworker\RadixDLT\Types\Primitives\Address;
use Techworker\RadixDLT\Types\Primitives\String_;

class ParticleIndex
{
    /**
     * ParticleIndex constructor.
     * @param Address $address
     * @param String_ $unique
     */
    protected function __construct(
        protected Address $address,
        protected String_ $unique
    ) {
    }

    public static function fromArray(array $json)
    {
    }

    public function toArray(): array
    {
    }

    public static function fromJson(array | string $json): self
    {
        if (is_string($json)) {
            throw new \InvalidArgumentException('Invalid.');
        }

        $address = Address::fromJson((string) $json['address']);
        $unique = String_::fromJson((string) $json['unique']);
        return new self($address, $unique);
    }

    public function toJson(): array | string
    {
        $json = [];
        $json['address'] = $this->address->toJson();
        $json['unique'] = $this->unique->toJson();
        return $json;
    }
}
