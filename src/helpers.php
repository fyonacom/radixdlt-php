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

namespace Techworker\RadixDLT;

use Elliptic\EC;
use Techworker\RadixDLT\Crypto\Keys\Curves\Secp256k1;
use Tuupola\Base58;

/**
 * @param array $data
 * @param int $offset
 * @param int $length
 * @return int[]
 */
function radixHash(array $data, int $offset = 0, int $length = 0) : array {
    if ($offset !== 0) {
        $data = array_slice($data, $offset, $length);
    }

    $last = bytesToBinary($data);
    for($i = 0; $i < Radix::RADIX_HASH_ROUNDS; $i++) {
        $context = hash_init(Radix::RADIX_HASH_ALG);
        hash_update($context, $last);
        $last = hash_final($context, true);
    }

    return binaryToBytes($last);
}

/**
 * temp
 * @param string $keyType
 * @return EC
 */
function ec(string $keyType = Secp256k1::class) : EC {
    return new EC($keyType);
}

/**
 * temp
 * @return Base58
 */
function bs58() : Base58 {
    return new Base58(["characters" => Base58::BITCOIN]);
}

/**
 * @param string $string
 * @param string|null $enc
 * @return int[]
 */
function encToBytes(string $string, ?string $enc = 'hex') : array {
    return match ($enc) {
        'hex' => hexToBytes($string),
        'base58' => base58ToBytes($string),
        default => binaryToBytes($string),
    };
}

/**
 * @param int[] $bytes
 * @param string|null $enc
 * @return string|int[]
 */
function bytesToEnc(array $bytes, ?string $enc = 'hex') : string|array {
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
 *
 * @param string $string
 * @return string
 */
function stringToHex(string $string) : string {
    /** @var string */
    return unpack('H*', $string)[1];
}

/**
 * Converts the given bytes to hex.
 *
 * @param array $bytes
 * @return string
 */
function bytesToHex(array $bytes) : string {
    return stringToHex(bytesToBinary($bytes));
}

/**
 * @param array $bytes
 * @return string
 */
function bytesToBinary(array $bytes) : string {
    return pack("C*", ...$bytes);
}

/**
 * @param array $bytes
 * @return string
 */
function bytesToBase64(array $bytes) : string {
    return base64_encode(bytesToBinary($bytes));
}

/**
 * @param string $base64
 * @return int[]
 */
function base64ToBytes(string $base64) : array {
    return binaryToBytes(base64_decode($base64));
}

/**
 *
 * @param string $string
 * @return int[]
 */
function binaryToBytes(string $string) : array {
    /** @var int[] */
    return array_values(unpack('C*', $string));
}

/**
 * @param string $hex
 * @return int[]
 */
function hexToBytes(string $hex) : array {
    return binaryToBytes(hexToString($hex));
}


/**
 * @param string $hex
 * @return string
 */
function hexToString(string $hex) : string {
    return pack('H*', $hex);
}

/**
 * @param string $bs58
 * @return int[]
 */
function base58ToBytes(string $bs58) : array {
    return binaryToBytes(bs58()->decode($bs58));
}

/**
 * @param int[] $bytes
 * @return string
 */
function bytesToBase58(array $bytes) : string {
    return bs58()->encode(bytesToBinary($bytes));
}

/**
 * @param int $value
 * @return int[]
 */
function uInt32ToBytes(int $value) : array {
    return binaryToBytes(pack('N*', $value));
}

/**
 * @param int[] $targetBytes
 * @param int[] $writeBytes
 * @param int $offset
 * @return int
 */
function writeBytes(array &$targetBytes, array $writeBytes, int $offset = 0) : int{
    $length = count($writeBytes);
    array_splice($targetBytes, $offset, $length, $writeBytes);
    $offset += $length;
    return $offset;
}

/**
 * @param int[] $target
 * @param int $value
 * @param int $offset
 * @return int
 */
function writeUInt32BE(array &$target, int $value, int $offset = 0) : int {
    return writeBytes($target, uInt32ToBytes($value), $offset);
}

function mapJsonToTypes(array $json) {

}
