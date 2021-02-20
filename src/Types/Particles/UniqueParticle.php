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

use Techworker\RadixDLT\Types\Core\Address;
use Techworker\RadixDLT\Types\Core\String_;

class UniqueParticle extends AbstractParticle
{
    /**
     * UniqueParticle constructor.
     * @param Address $address
     * @param String_ $name
     * @param int|null $nonce
     */
    protected function __construct(
        protected Address $address,
        protected String_ $name,
        protected ?int $nonce = null
    ) {
    }

    /**
     * @return Address[]
     */
    public function getAddresses(): array
    {
        return [$this->address];
    }

    /**
     * @param array|string $json
     * @throws \Exception
     */
    public static function fromJson(array | string $json): self
    {
        if (is_string($json)) {
            throw new \InvalidArgumentException('Invalid.');
        }

        $address = Address::fromJson((string) $json['address']);
        $name = String_::fromJson((string) $json['name']);
        // TODO: create nonce if not set?
        $nonce = (int) $json['nonce'];
        return new self($address, $name, $nonce);
    }

    /**
     * @return array|string
     * @throws \Exception
     */
    public function toJson(): array | string
    {
        $json = [];
        $json['address'] = $this->address->toJson();
        $json['name'] = $this->name->toJson();
        $json['nonce'] = $this->nonce;
        return $json;
    }
}
