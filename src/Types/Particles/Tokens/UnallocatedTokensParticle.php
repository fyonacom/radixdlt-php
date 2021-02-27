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

namespace Techworker\RadixDLT\Types\Particles\Tokens;

use Techworker\RadixDLT\Serialization\Attributes\DsonProperty;
use Techworker\RadixDLT\Serialization\Attributes\JsonProperty;
use Techworker\RadixDLT\Serialization\Attributes\Serializer;
use Techworker\RadixDLT\Types\Particles\Particle;
use Techworker\RadixDLT\Types\Primitives\RRI;
use Techworker\RadixDLT\Types\Primitives\UID;
use Techworker\RadixDLT\Types\Primitives\UInt256;

#[Serializer('radix.particles.unallocated_tokens')]
class UnallocatedTokensParticle extends Particle
{
    public function __construct(
        #[JsonProperty]
        #[DsonProperty]
        protected int $version,
        #[JsonProperty]
        #[DsonProperty]
        protected UID $hid,
        #[JsonProperty(arraySubType: UID::class)]
        #[DsonProperty]
        protected array $destinations,
        #[JsonProperty]
        #[DsonProperty]
        protected UInt256 $amount,
        #[JsonProperty]
        #[DsonProperty]
        protected UInt256 $granularity,
        #[JsonProperty]
        #[DsonProperty]
        protected TokenPermission $permissions,
        #[JsonProperty]
        #[DsonProperty]
        protected int $nonce,
        #[JsonProperty]
        #[DsonProperty]
        protected RRI $tokenDefinitionReference,
    ) {
    }
}
