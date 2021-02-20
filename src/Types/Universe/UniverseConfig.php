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

use Techworker\RadixDLT\Serialization\Interfaces\FromJsonInterface;
use Techworker\RadixDLT\Serialization\Interfaces\ToJsonInterface;
use Techworker\RadixDLT\Types\Primitives\Bytes;
use Techworker\RadixDLT\Types\Primitives\String_;

final class UniverseConfig implements FromJsonInterface, ToJsonInterface
{
    public const TYPE_PRODUCTION = 0;

    public const TYPE_TEST = 1;

    public const TYPE_DEVELOPMENT = 2;

    private int $magicByte;

    private function __construct(
        protected int $magic,
        protected Bytes $creator,
        protected int $port,
        protected Bytes $signatureR,
        protected Bytes $signatureS,
        protected String_ $name,
        protected String_ $description,
        protected int $type,
        protected int $timestamp,
        protected array $genesis
    ) {
        $this->magicByte = $this->magic & 0xff;
    }

    public static function fromJson(array | string $json): static
    {
        if (is_string($json)) {
            $json = (array) json_decode($json, true);
        }

        $data = [];
        $data['magic'] = (int) $json['magic'];
        $data['creator'] = Bytes::fromJson((string) $json['creator']);
        $data['port'] = (int) $json['port'];
        $data['signatureR'] = Bytes::fromJson((string) $json['signature.r']);
        $data['signatureS'] = Bytes::fromJson((string) $json['signature.s']);
        $data['name'] = String_::fromJson((string) $json['name']);
        $data['description'] = String_::fromJson((string) $json['description']);
        $data['type'] = (int) $json['type'];
        $data['timestamp'] = (int) $json['timestamp'];
        $data['genesis'] = [];

        return new self(...$data);
    }

    public function toJson(): array | string
    {
        $data = [];
        $data['magic'] = $this->magic;
        $data['creator'] = $this->creator->toJson();
        $data['port'] = $this->port;
        $data['signatureR'] = $this->signatureR->toJson();
        $data['signatureS'] = $this->signatureS->toJson();
        $data['name'] = $this->name->toJson();
        $data['description'] = $this->description->toJson();
        $data['type'] = $this->type;
        $data['timestamp'] = $this->timestamp;
        $data['genesis'] = [];

        return $data;
    }
}
