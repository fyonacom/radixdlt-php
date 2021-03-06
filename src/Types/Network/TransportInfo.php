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

namespace Techworker\RadixDLT\Types\Network;

use Techworker\RadixDLT\Serialization\Attributes\DsonProperty;
use Techworker\RadixDLT\Serialization\Attributes\JsonProperty;
use Techworker\RadixDLT\Serialization\Attributes\Serializer;
use Techworker\RadixDLT\Types\Primitives\Hash;
use Techworker\RadixDLT\Types\Primitives\String_;

#[Serializer(name: 'network.transport_info')]
class TransportInfo
{
    public function __construct(
        #[JsonProperty(key: 'metadata')]
        #[DsonProperty]
        protected array $metaData,
        #[JsonProperty]
        #[DsonProperty]
        protected String_ $name
    ) {
    }
}
