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

use BN\BN;
use Techworker\RadixDLT\Serialization\Attributes\DsonProperty;
use Techworker\RadixDLT\Serialization\Attributes\JsonProperty;
use Techworker\RadixDLT\Serialization\Attributes\Serializer;
use Techworker\RadixDLT\Types\Particles\Particle;
use Techworker\RadixDLT\Types\Primitives\Address;
use Techworker\RadixDLT\Types\Primitives\Hash;
use Techworker\RadixDLT\Types\Primitives\RRI;
use Techworker\RadixDLT\Types\Primitives\String_;
use Techworker\RadixDLT\Types\Primitives\UID;
use Techworker\RadixDLT\Types\Primitives\UInt256;

#[Serializer('radix.particles.mutable_supply_token_definition')]
class MutableSupplyTokenDefinitionParticle extends Particle
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
        protected String_ $name,
        #[JsonProperty]
        #[DsonProperty]
        protected String_ $description,
        #[JsonProperty]
        #[DsonProperty]
        protected UInt256 $granularity,
        #[JsonProperty]
        #[DsonProperty]
        protected String_ $iconUrl,
        #[JsonProperty]
        #[DsonProperty]
        protected String_ $url,
        #[JsonProperty]
        #[DsonProperty]
        protected TokenPermission $permissions,
    ) {
        if ($granularity->getBn()->lt(new BN('0'))) {
            throw new \InvalidArgumentException('Granularity has to be larger than 0');
        }
    }

    public function getOwner(): Address
    {
        return $this->rri->getAddress();
    }

    public function getSymbol(): string
    {
        return $this->rri->getSymbol();
    }

    public function getAddresses(): array
    {
        return [$this->rri->getAddress()->getUID()];
    }
}
