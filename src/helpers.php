<?php

namespace Techworker\RadixDLT;

use Elliptic\EC;
use Tuupola\Base58;

function radixHash(array $data, int $offset = 0, int $length = 0) : array {
    if ($offset !== 0) {
        $data = array_slice($data, $offset, $length);
    }

    $last = bytesToBin($data);
    for($i = 0; $i < Radix::RADIX_HASH_ROUNDS; $i++) {
        $context = hash_init(Radix::RADIX_HASH_ALG);
        hash_update($context, $last);
        $last = hash_final($context, true);
    }

    return stringToBytes($last);
}

// temp
function ec(string $keyType = CurveInfo::SECP256K1) : EC {
    return new EC($keyType);
}

// temp
function bs58() : Base58 {
    return new Base58(["characters" => Base58::BITCOIN]);
}

function encToBytes(string $string, ?string $enc = 'hex') : array {
    return match ($enc) {
        'hex' => hexToBytes($string),
        'base58' => bs58ToBytes($string),
        default => stringToBytes($string),
    };
}

function bytesToEnc(array $bytes, ?string $enc = 'hex') : string|array {
    return match ($enc) {
        'bytes' => $bytes,
        'hex' => bytesToHex($bytes),
        'base58' => bytesToBs58($bytes),
        default => bytesToBin($bytes),
    };
}

/**
 * Converts a string/binary to a hex string.
 *
 * @param string $string
 * @return string
 */
function stringToHex(string $string) : string {
    return unpack('H*', $string)[1];
}

/**
 * Converts the given bytes to hex.
 *
 * @param array $bytes
 * @return string
 */
function bytesToHex(array $bytes) : string {
    return stringToHex(bytesToBin($bytes));
}

/**
 * @param array $bytes
 * @return string
 */
function bytesToBin(array $bytes) : string {
    return pack("C*", ...$bytes);
}

/**
 *
 * @param string $string
 * @return array
 */
function stringToBytes(string $string) : array {
    return array_values(unpack('C*', $string));
}

/**
 * @param string $hex
 * @return array
 */
function hexToBytes(string $hex) : array {
    return stringToBytes(hexToString($hex));
}


/**
 * @param string $hex
 * @return string
 */
function hexToString(string $hex) : string {
    return pack('H*', $hex);
}

function bs58ToBytes(string $bs58) {
    return stringToBytes(bs58()->decode($bs58));
}

function bytesToBs58(array $bytes) : string {
    return bs58()->encode(bytesToBin($bytes));
}

function uInt32ToBytes(int $value) : array {
    return stringToBytes(pack('N*', $value));
}

function writeBytes(array &$targetBytes, array $writeBytes, int $offset = 0) : int{
    $length = count($writeBytes);
    array_splice($target, $offset, $length, $writeBytes);
    $offset += $length;
    return $offset;
}

function writeUInt32BE(array &$target, int $value, int $offset = 0) : int {
    return writeBytes($target, uInt32ToBytes($value), $offset);
}

function readInt64BE(array $source, int $offset = 0) {
    list(, $hihi, $hilo, $lohi, $lolo) = unpack('n*', array_slice($source, $offset, 8));
    return ($hihi * (0xffff+1) + $hilo) * (0xffffffff+1) +
        ($lohi * (0xffff+1) + $lolo);
}
