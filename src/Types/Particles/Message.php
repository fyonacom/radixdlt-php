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
use Techworker\RadixDLT\Types\Primitives\Address;
use Techworker\RadixDLT\Types\Primitives\Bytes;
use Techworker\RadixDLT\Types\Primitives\UID;

#[Serializer(name: 'radix.particles.message')]
class Message extends Particle
{
    public function __construct(
        #[JsonProperty]
        #[DsonProperty]
        protected UID $hid,
        #[JsonProperty]
        #[DsonProperty]
        protected Bytes $bytes,
        #[JsonProperty(arraySubType: UID::class)]
        #[DsonProperty]
        protected array $destinations,
        #[JsonProperty]
        #[DsonProperty]
        protected Address $from,
        #[JsonProperty]
        #[DsonProperty]
        protected Address $to,
        #[JsonProperty]
        #[DsonProperty]
        protected int $version,
        #[JsonProperty]
        #[DsonProperty]
        protected int $nonce
    ) {
    }
}
