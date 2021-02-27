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

namespace Techworker\RadixDLT\Types\Universe;

use Techworker\RadixDLT\Serialization\Attributes\DsonProperty;
use Techworker\RadixDLT\Serialization\Attributes\JsonProperty;
use Techworker\RadixDLT\Serialization\Attributes\Serializer;
use Techworker\RadixDLT\Types\Complex;
use Techworker\RadixDLT\Types\Primitives\Bytes;
use Techworker\RadixDLT\Types\Primitives\String_;
use Techworker\RadixDLT\Types\Primitives\UID;

#[Serializer(name: 'radix.universe')]
class UniverseConfig extends Complex
{
    public const TYPE_PRODUCTION = 0;

    public const TYPE_TEST = 1;

    public const TYPE_DEVELOPMENT = 2;

    private int $magicByte;

    public function __construct(
        #[JsonProperty]
        #[DsonProperty]
        protected int $magic,
        #[JsonProperty]
        #[DsonProperty]
        protected Bytes $creator,
        #[JsonProperty]
        #[DsonProperty]
        protected UID $hid,
        #[JsonProperty]
        #[DsonProperty]
        protected int $port,
        #[JsonProperty('signature.s')]
        #[DsonProperty]
        protected Bytes $signatureR,
        #[JsonProperty('signature.r')]
        #[DsonProperty]
        protected Bytes $signatureS,
        #[JsonProperty]
        #[DsonProperty]
        protected String_ $name,
        #[JsonProperty]
        #[DsonProperty]
        protected String_ $description,
        #[JsonProperty]
        #[DsonProperty]
        protected int $type,
        #[JsonProperty]
        #[DsonProperty]
        protected int $timestamp,
        #[JsonProperty]
        #[DsonProperty]
        protected array $genesis,
        #[JsonProperty]
        #[DsonProperty]
        protected int $version
    ) {
        $this->magicByte = $this->magic & 0xff;
    }
}
