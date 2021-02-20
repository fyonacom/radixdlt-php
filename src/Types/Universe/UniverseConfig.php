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

use Techworker\RadixDLT\Types\Core\Bytes;
use Techworker\RadixDLT\Types\Core\EUID;
use Techworker\RadixDLT\Types\Core\String_;

final class UniverseConfig
{
    public const TYPE_PRODUCTION = 0;

    public const TYPE_TEST = 1;

    public const TYPE_DEVELOPMENT = 2;

    private int $magicByte;

    private function __construct(
        protected int $magic,
        protected EUID $hid,
        protected Bytes $creator,
        protected Bytes $signatureR,
        protected Bytes $signatureS,
        protected String_ $name,
        protected String_ $description,
        protected int $type,
        protected int $version,
        protected int $port,
        protected int $timestamp,
        protected array $genesis
    ) {
        $this->magicByte = $this->magic & 0xff;
    }

    public static function fromArray(array $json)
    {
    }

    public static function fromJson(array | string $json): self
    {
        if (is_string($json)) {
            throw new \InvalidArgumentException('UniverseConfig needs an array');
        }

        $genesis = [];
        $data = [];
        $data['magic'] = (int) $json['magic'];
        $data['hid'] = EUID::fromJson((string) $json['hid']);
        $data['creator'] = Bytes::fromJson((string) $json['creator']);
        $data['signatureR'] = Bytes::fromJson((string) $json['signature.r']);
        $data['signatureS'] = Bytes::fromJson((string) $json['signature.s']);
        $data['name'] = String_::fromJson((string) $json['name']);
        $data['description'] = String_::fromJson((string) $json['description']);
        $data['type'] = (int) $json['type'];
        $data['version'] = (int) $json['version'];
        $data['port'] = (int) $json['port'];
        $data['timestamp'] = (int) $json['timestamp'];
        $data['genesis'] = $genesis;

        return new self(...$data);
    }

    public function toJson(): array | string
    {
        $json = [];
        $json['magic'] = $this->magic;
        $json['hid'] = $this->hid->toJson();
        $json['creator'] = $this->creator->toJson();
        $json['signatureR'] = $this->signatureR->toJson();
        $json['signatureS'] = $this->signatureS->toJson();
        $json['name'] = $this->name->toJson();
        $json['description'] = $this->description->toJson();
        $json['type'] = $this->type;
        $json['version'] = $this->version;
        $json['port'] = $this->port;
        $json['timestamp'] = $this->timestamp;
        $json['genesis'] = $this->genesis;
        return $json;
    }
}
