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

/**
 * Class AbstractPrimitive
 *
 * Basic primitive with common utility functionality.
 * @psalm-consistent-constructor
 */
trait BytesTrait
{
    private array $bytes;

    public function countBytes()
    {
        return count($this->bytes);
    }

    /**
     * @return int[]
     */
    public function slice(int $offset, int $length = null): array
    {
        return array_slice($this->bytes, $offset, $length);
    }

    public function at(int $index): int
    {
        return $this->bytes[$index];
    }

    public function exists(int $index): bool
    {
        return isset($this->bytes[$index]);
    }

    public function swap(int $index, int $to): void
    {
        $this->bytes[$index] = $to;
    }

    /**
     * Gets the bytes of this instance.
     *
     * @return int[]
     */
    public function toBytes(): array
    {
        return $this->bytes;
    }

    /**
     * Gets the base58 representation.
     */
    public function toBase58(): string
    {
        return bytesToBase58($this->bytes);
    }

    /**
     * Gets the hex representation.
     */
    public function toHex(): string
    {
        return bytesToHex($this->bytes);
    }

    /**
     * Gets the binary string representation.
     */
    public function toBinary(): string
    {
        return bytesToBinary($this->bytes);
    }

    /**
     * Gets the binary string representation.
     */
    public function toBase64(): string
    {
        return bytesToBase64($this->bytes);
    }

    /**
     * Gets the representation based on the given encoding.
     *
     * @param mixed ...$args
     * @return array|string
     */
    public function to(string $enc, ...$args): array | string
    {
        $enc = strtolower($enc);

        $method = 'to' . ucfirst($enc);
        if (method_exists($this, $method)) {
            /** @var string|array */
            return call_user_func_array([$this, $method], $args);
        }

        throw new \InvalidArgumentException('Invalid to encoding: ' . $enc);
    }

    /**
     * Creates a new primitive instance from the given hex string.
     *
     * @return static
     */
    public static function fromHex(string $hex): static
    {
        return new static(hexToBytes($hex));
    }

    /**
     * Creates a new primitive instance from the given binary string.
     *
     * @return static
     */
    public static function fromBinary(string $binary): static
    {
        return new static(binaryToBytes($binary));
    }

    /**
     * Creates a new primitive instance from the given base58 string.
     *
     * @return static
     */
    public static function fromBase58(string $base58): static
    {
        return new static(base58ToBytes($base58));
    }

    /**
     * Creates a new primitive instance from the given base58 string.
     *
     * @return static
     */
    public static function fromBase64(string $base64): static
    {
        return new static(base64ToBytes($base64));
    }

    /**
     * Creates a new instance of the implementing class.
     *
     * @param int[] $bytes
     * @return static
     */
    public static function fromBytes(array $bytes): static
    {
        return new static($bytes);
    }

    /**
     * Initializes a new instance.
     *
     * @param string|int[] $data
     */
    public static function from(string | array $data, string $enc, mixed ...$args): self
    {
        $enc = strtolower($enc);
        $method = 'from' . ucfirst($enc);
        if (method_exists(static::class, $method)) {
            /** @var self */
            return call_user_func_array([static::class, $method], $args);
        }

        throw new \Exception('Invalid from encoding: ' . $enc);
    }

    /**
     * AbstractPrimitive constructor.
     * @param int[] $bytes
     */
    protected function initBytes(array $bytes): void
    {
        $this->bytes = $bytes;
    }
}
