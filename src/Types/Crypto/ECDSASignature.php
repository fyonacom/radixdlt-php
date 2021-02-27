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

namespace Techworker\RadixDLT\Types\Crypto;

use Techworker\RadixDLT\Serialization\Attributes\DsonProperty;
use Techworker\RadixDLT\Serialization\Attributes\JsonProperty;
use Techworker\RadixDLT\Serialization\Attributes\Serializer;
use Techworker\RadixDLT\Serialization\Interfaces\FromJsonInterface;
use Techworker\RadixDLT\Types\Primitives\Bytes;

#[Serializer(name: 'crypto.ecdsa_signature')]
class ECDSASignature
{
    public function __construct(
        #[JsonProperty]
        #[DsonProperty]
        protected Bytes $r,
        #[JsonProperty]
        #[DsonProperty]
        protected Bytes $s,
        #[JsonProperty]
        #[DsonProperty]
        protected int $version
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
