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

namespace Techworker\RadixDLT\Crypto\Keys\Curves;

use Techworker\RadixDLT\Crypto\Keys\AbstractCurve;

/**
 * Class Secp256k1
 *
 * A secp256k1 key can be < 32 bytes, see https://stackoverflow.com/questions/62938091/why-are-secp256k1-privatekeys-not-always-32-bytes-in-nodejs
 */
class Secp256k1 extends AbstractCurve
{
    public static function getName(): string
    {
        return 'secp256k1';
    }

    public static function getPrivateKeyLengths(): array
    {
        return [32, 29, 30, 31];
    }

    public static function getPublicKeyLengths(): array
    {
        return [33, 32];
    }
}
