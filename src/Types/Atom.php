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

namespace Techworker\RadixDLT\Types;

use Techworker\RadixDLT\Serialization\Attributes\DsonProperty;
use Techworker\RadixDLT\Serialization\Attributes\JsonProperty;
use Techworker\RadixDLT\Serialization\Attributes\Serializer;
use Techworker\RadixDLT\Types\Crypto\ECDSASignature;
use Techworker\RadixDLT\Types\Particles\ParticleGroup;
use Techworker\RadixDLT\Types\Primitives\Hash;
use Techworker\RadixDLT\Types\Primitives\UID;

#[Serializer(name: 'radix.atom')]
class Atom
{
    public function __construct(
        #[JsonProperty]
        #[DsonProperty]
        protected Hash $hid,
        #[JsonProperty(arraySubType: ParticleGroup::class)]
        #[DsonProperty]
        protected array $particleGroups,
        #[JsonProperty(arraySubType: ECDSASignature::class)]
        #[DsonProperty]
        protected array $signatures
    ) {
    }
}
