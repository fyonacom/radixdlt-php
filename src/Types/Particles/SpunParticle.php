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

/**
 * Class RadixSpunParticle
 * @package Techworker\RadixDLT\Types\Particles
 */
#[Serializer('radix.spun_particle')]
class SpunParticle
{
    public const SPIN_UP = 1;

    public const SPIN_NEUTRAL = 0;

    public const SPIN_DOWN = -1;

    public function __construct(
        #[JsonProperty]
        #[DsonProperty]
        protected int $spin,
        #[JsonProperty]
        #[DsonProperty]
        protected Particle $particle,
    ) {
    }
}
