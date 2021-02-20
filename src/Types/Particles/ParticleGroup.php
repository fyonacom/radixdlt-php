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

class ParticleGroup
{
    /**
     * RadixParticleGroup constructor.
     * @param SpunParticle[] $particles
     * @param array $metaData
     */
    public function __construct(
        protected array $particles = [],
        protected array $metaData = []
    ) {
    }
}
