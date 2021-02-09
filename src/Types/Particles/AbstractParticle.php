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

use CBOR\AbstractCBORObject;
use Techworker\RadixDLT\Serialization\FromJsonInterface;
use Techworker\RadixDLT\Serialization\ToJsonInterface;
use Techworker\RadixDLT\Types\Core\Address;
use Techworker\RadixDLT\Types\Universe\UniverseConfig;

/**
 * Class Particle
 * @package Techworker\RadixDLT\Types\Particles
 */
abstract class AbstractParticle
    implements ToJsonInterface, FromJsonInterface
{
    abstract public function getAddresses() : array;

    public function getDestinations() : array {
        return array_map(
            fn(Address $address) => $address->getUID(),
            $this->getAddresses()
        );
    }

    public static function fromJson(array|string $json): AbstractParticle|SpunParticle
    {
        if(is_string($json)) {
            throw new \Exception('..');
        }

        return match($json['serializer']) {
            'a' => SpunParticle::fromJson($json),
            default => UniqueParticle::fromJson($json)
        };
    }

    public function toJson(): array|string
    {
        return '';
    }


}
