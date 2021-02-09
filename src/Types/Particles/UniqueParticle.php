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

use Techworker\RadixDLT\Serialization\FromJsonInterface;
use Techworker\RadixDLT\Serialization\ToJsonInterface;
use Techworker\RadixDLT\Types\Core\Address;
use Techworker\RadixDLT\Types\Core\RRI;
use Techworker\RadixDLT\Types\Core\String;

class UniqueParticle extends AbstractParticle
    implements ToJsonInterface, FromJsonInterface
{

    protected function __construct(
        protected Address $address,
        protected string $name,
        protected ?int $nonce = null
    ) {
    }

    public function getAddresses(): array
    {
        return [$this->address];
    }


    public static function fromJson(array|string $json): UniqueParticle
    {
        if(is_string($json)) {
            throw new \InvalidArgumentException('Invalid.');
        }

        $address = Address::fromJson((string)$json['address']);
        $name = String::fromJson((string)$json['name']);
        // TODO: create nonce if not set?
        $nonce = (int)$json['nonce'];
        return new self($address, $name, $nonce);
    }

    public function toJson(): array|string
    {
        $json = [];
        $json['address'] = $this->address->toJson();
        $json['name'] = $this->name->toJson();
        $json['nonce'] = $this->nonce;
        return $json;
    }

}
