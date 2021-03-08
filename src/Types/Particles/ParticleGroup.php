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

use Techworker\RadixDLT\Serialization\Attributes\DsonProperty;
use Techworker\RadixDLT\Serialization\Attributes\JsonProperty;
use Techworker\RadixDLT\Serialization\Attributes\Serializer;

#[Serializer(name: 'radix.particle_group')]
class ParticleGroup
{
    /**
     * RadixParticleGroup constructor.
     * @param SpunParticle[] $particles
     */
    public function __construct(
        #[JsonProperty]
        #[DsonProperty]
        protected array $particles = [],
    ) {
    }
}
