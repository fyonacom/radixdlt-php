<?php

namespace Techworker\RadixDLT;

use Techworker\RadixDLT\Crypto\Keys\AbstractKeyPair;
use Techworker\RadixDLT\Crypto\Keys\Curves\Secp256k1;

final class Radix {
    public const RADIX_HASH_ROUNDS = 2;
    public const RADIX_HASH_ALG = 'sha256';

    /**
     * @var AbstractKeyPair[]
     */
    public const RADIX_CURVES = [
        Secp256k1::class
    ];

}
