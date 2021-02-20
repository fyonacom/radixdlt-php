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
use Techworker\RadixDLT\Types\Primitives\Bytes;
use Techworker\RadixDLT\Types\Primitives\Hash;
use Techworker\RadixDLT\Types\Primitives\String_;

final class UniverseConfig implements
    FromJsonInterface, \Stringable
{
    public const TYPE_PRODUCTION = 0;

    public const TYPE_TEST = 1;

    public const TYPE_DEVELOPMENT = 2;

    private int $magicByte;

    private function __construct(
        protected int $magic,
        protected Bytes $creator,
        protected Hash $hid,
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

    public static function fromJson(array|string $json): static
    {
        if (is_string($json)) {
            $json = (array)json_decode($json, true);
        }

        $data = [];
        $data['magic'] = (int)$json['magic'];
        $data['hid'] = Hash::fromJson((string)$json['hid']);
        $data['creator'] = Bytes::fromJson((string)$json['creator']);
        $data['port'] = (int)$json['port'];
        $data['signatureR'] = Bytes::fromJson((string)$json['signature.r']);
        $data['signatureS'] = Bytes::fromJson((string)$json['signature.s']);
        $data['name'] = String_::fromJson((string)$json['name']);
        $data['description'] = String_::fromJson((string)$json['description']);
        $data['type'] = (int)$json['type'];
        $data['timestamp'] = (int)$json['timestamp'];
        $data['genesis'] = [];

        return new self(...$data);
    }

    public function getMagicByte(): int
    {
        return $this->magicByte;
    }

    public function getMagic(): int
    {
        return $this->magic;
    }

    public function getCreator(): Bytes
    {
        return $this->creator;
    }

    public function getHid(): Hash
    {
        return $this->hid;
    }

    public function getPort(): int
    {
        return $this->port;
    }

    public function getSignatureR(): Bytes
    {
        return $this->signatureR;
    }

    public function getSignatureS(): Bytes
    {
        return $this->signatureS;
    }

    public function getName(): String_
    {
        return $this->name;
    }

    public function getDescription(): String_
    {
        return $this->description;
    }

    public function getType(): int
    {
        return $this->type;
    }

    public function isProduction(): bool
    {
        return $this->type === self::TYPE_PRODUCTION;
    }

    public function isDevelopment(): bool
    {
        return $this->type === self::TYPE_DEVELOPMENT;
    }

    public function isTest(): bool
    {
        return $this->type === self::TYPE_TEST;
    }

    public function getTimestamp(): int
    {
        return $this->timestamp;
    }

    /**
     * @return array
     */
    public function getGenesis(): array
    {
        return $this->genesis;
    }

    public function __toString()
    {
        return $this->name . " " . $this->magic . " " . euid();
    }


}
