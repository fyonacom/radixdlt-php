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
use Techworker\RadixDLT\Types\Particles\Tokens\TokenPermission;
use Techworker\RadixDLT\Types\Primitives\Address;
use Techworker\RadixDLT\Types\Primitives\Bytes;
use Techworker\RadixDLT\Types\Primitives\Hash;
use Techworker\RadixDLT\Types\Primitives\RRI;
use Techworker\RadixDLT\Types\Primitives\UID;
use Techworker\RadixDLT\Types\Primitives\UInt256;

#[Serializer(name: 'radix.particles.staked_tokens')]
class StakedTokens extends Particle
{
    public function __construct(
        #[JsonProperty]
        #[DsonProperty]
        protected UInt256 $amount,
        #[JsonProperty]
        #[DsonProperty]
        protected Hash $hid,
        #[JsonProperty]
        #[DsonProperty]
        protected Address $address,
        #[JsonProperty]
        #[DsonProperty]
        protected UInt256 $granularity,
        #[JsonProperty]
        #[DsonProperty]
        protected TokenPermission $permissions,
        #[JsonProperty]
        #[DsonProperty]
        protected Address $delegateAddress,
        #[JsonProperty]
        #[DsonProperty]
        protected int $nonce,
        #[JsonProperty]
        #[DsonProperty]
        protected RRI $tokenDefinitionReference
    ) {
    }
}
