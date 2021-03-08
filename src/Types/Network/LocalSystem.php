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
use Techworker\RadixDLT\Types\Primitives\UInt256;

#[Serializer(name: 'api.local_system')]
class LocalSystem
{
    public function __construct(
        #[JsonProperty(arraySubType: TransportInfo::class)]
        #[DsonProperty]
        protected array $transports,
        #[JsonProperty]
        #[DsonProperty]
        protected Agent $agent,
        #[JsonProperty]
        #[DsonProperty]
        protected Hash $hid,
        #[JsonProperty]
        #[DsonProperty]
        protected UInt256 $nid,
        #[JsonProperty]
        #[DsonProperty]
        protected int $processors,
        #[JsonProperty]
        #[DsonProperty]
        protected String_ $key,
        #[JsonProperty]
        #[DsonProperty]
        protected int $timestamp,
        #[JsonProperty]
        #[DsonProperty]
        protected array $info
    ) {
    }

    public static function fromJson(array | string $json): FromJsonInterface
    {
        $data = [];
        $data['r'] = Bytes::fromJson((string) $json['r']);
        $data['s'] = Bytes::fromJson((string) $json['s']);
        $data['version'] = (int) $json['version'];
        return new self(...$data);
    }

    public function toJson(): array | string
    {
        $json = [];
        $json['r'] = $this->r->toJson();
        $json['s'] = $this->s->toJson();
        $json['version'] = $this->version;
        return $json;
    }
}
