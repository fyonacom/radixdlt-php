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

/**
 * Class Particle
 * @package Techworker\RadixDLT\Types\Particles
 */
abstract class AbstractParticle
{
    public function getAddresses(): array {
        return [];
    }

    public function getDestinations(): array
    {
        return array_map(
            fn (Address $address) => $address->getUID(),
            $this->getAddresses()
        );
    }
}
