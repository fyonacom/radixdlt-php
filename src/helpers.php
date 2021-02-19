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

use Tuupola\Base58;

/**
 * @return int[]
 */
function radixHash(array $data, int $offset = 0, int $length = 0): array
{
    if ($offset !== 0) {
        $data = array_slice($data, $offset, $length);
    }

    $last = bytesToBinary($data);
    for ($i = 0; $i < \Techworker\RadixDLT\Radix::RADIX_HASH_ROUNDS; $i++) {
        $context = hash_init(\Techworker\RadixDLT\Radix::RADIX_HASH_ALG);
        hash_update($context, $last);
        $last = hash_final($context, true);
    }

    return binaryToBytes($last);
}

/**
 * temp
 */
function bs58(): Base58
{
    return new Base58([
        'characters' => Base58::BITCOIN,
    ]);
}

/**
 * @return int[]
 */
function encToBytes(string $string, ?string $enc = 'hex'): array
{
    return match ($enc) {
        'hex' => hexToBytes($string),
        'base58' => base58ToBytes($string),
    default => binaryToBytes($string),
    };
}

/**
 * @param int[] $bytes
 * @return string|int[]
 */
function bytesToEnc(array $bytes, ?string $enc = 'hex'): string | array
{
    return match ($enc) {
        'bytes' => $bytes,
        'hex' => bytesToHex($bytes),
        'base58' => bytesToBase58($bytes),
        'base64' => bytesToBase64($bytes),
    default => bytesToBinary($bytes),
    };
}

/**
 * Converts a string/binary to a hex string.
 */
function stringToHex(string $string): string
{
    /** @var string */
    return unpack('H*', $string)[1];
}

/**
 * Converts the given bytes to hex.
 */
function bytesToHex(array $bytes): string
{
    return stringToHex(bytesToBinary($bytes));
}


function bytesToBinary(array $bytes): string
{
    return pack('C*', ...$bytes);
}


function bytesToBase64(array $bytes): string
{
    return base64_encode(bytesToBinary($bytes));
}

/**
 * @return int[]
 */
function base64ToBytes(string $base64): array
{
    return binaryToBytes(base64_decode($base64, true));
}

/**
 * @return int[]
 */
function binaryToBytes(string $string): array
{
    /** @var int[] */
    return array_values(unpack('C*', $string));
}

/**
 * @return int[]
 */
function hexToBytes(string $hex): array
{
    return binaryToBytes(hexToString($hex));
}



function hexToString(string $hex): string
{
    return pack('H*', $hex);
}

/**
 * @return int[]
 */
function base58ToBytes(string $bs58): array
{
    return binaryToBytes(bs58()->decode($bs58));
}

/**
 * @param int[] $bytes
 */
function bytesToBase58(array $bytes): string
{
    return bs58()->encode(bytesToBinary($bytes));
}

/**
 * @return int[]
 */
function uInt32ToBytes(int $value): array
{
    return binaryToBytes(pack('N*', $value));
}

/**
 * @param int[] $targetBytes
 * @param int[] $writeBytes
 */
function writeBytes(
    array &$targetBytes,
    array $writeBytes,
    int $offset = 0
): int {
    $length = count($writeBytes);
    array_splice($targetBytes, $offset, $length, $writeBytes);
    $offset += $length;
    return $offset;
}

/**
 * @param int[] $target
 */
function writeUInt32BE(
    array &$target,
    int $value,
    int $offset = 0
): int {
    return writeBytes($target, uInt32ToBytes($value), $offset);
}

/**
 * @param string ...$keys
 */
function radixConfig(string ...$keys): mixed
{
    /** @var array $config */
    $config = radix()->get(\Techworker\RadixDLT\Radix::CFG);
    if (count($keys) === 0) {
        return $config;
    }

    $accessKey = implode('.', $keys);
    if (isset($config[$accessKey])) {
        return $config[$accessKey];
    }

    foreach (explode('.', $accessKey) as $segment) {
        if (! is_array($config) || ! array_key_exists($segment, $config)) {
            throw new \InvalidArgumentException('Invalid config option: ' . $accessKey);
        }

        /** @var mixed $config */
        $config = $config[$segment];
    }

    return $config;
}


function radix(): \Techworker\RadixDLT\Radix
{
    return \Techworker\RadixDLT\Radix::getInstance();
}


function arrayMergeRecursiveDistinct(array &$array1, array &$array2): array
{
    $merged = $array1;

    /**
     * @var int|string $key
     * @var mixed $value
     */
    foreach ($array2 as $key => &$value) {
        if (is_array($value) && isset($merged[$key]) && is_array($merged[$key])) {
            $merged[$key] = arrayMergeRecursiveDistinct($merged[$key], $value);
        } else {
            /** @var mixed */
            $merged[$key] = $value;
        }
    }

    return $merged;
}
