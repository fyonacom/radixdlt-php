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

use Techworker\RadixDLT\Serialization\Attributes\Dson;
use Techworker\RadixDLT\Serialization\Attributes\DsonProperty;
use Techworker\RadixDLT\Serialization\Attributes\JsonProperty;
use Techworker\RadixDLT\Serialization\Attributes\Serializer;
use Techworker\RadixDLT\Types\Primitives\Hash;
use Techworker\RadixDLT\Types\Primitives\RRI;
use Techworker\RadixDLT\Types\Primitives\UID;

#[Dson(majorType: 5)]
#[Serializer(name: 'radix.particles.rri')]
class RRIParticle extends Particle
{
    public function __construct(
        #[JsonProperty]
        #[DsonProperty]
        protected Hash $hid,
        #[JsonProperty]
        #[DsonProperty]
        protected RRI $rri,
        #[JsonProperty]
        #[DsonProperty]
        protected int $nonce
    ) {
    }
}
