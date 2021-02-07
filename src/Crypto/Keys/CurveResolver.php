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

namespace Techworker\RadixDLT\Crypto\Keys;

use InvalidArgumentException;
use Techworker\RadixDLT\Crypto\Keys\Curves\Secp256k1;
use Techworker\RadixDLT\Radix;

class CurveResolver
{
    /**
     * Tries to determine a curve by the given public key length.
     *
     * @param int $length
     * @return string
     * @throws InvalidArgumentException
     */
    public static function curveByPublicKeyLength(int $length): string
    {
        foreach (Radix::RADIX_CURVES as $curve) {
            if (in_array($length, $curve::getPublicKeyLengths(), true)) {
                return $curve;
            }
        }

        throw new InvalidArgumentException(
            'Unable to identify a curve with public key length: ' . $length
        );
    }

    /**
     * Tries to determine a curve by the given public key length.
     *
     * @param int $length
     * @return string
     * @throws InvalidArgumentException
     */
    public static function curveByPrivateKeyLength(int $length): string
    {
        foreach (Radix::RADIX_CURVES as $curve) {
            if (in_array($length, $curve::getPrivateKeyLengths(), true)) {
                return $curve;
            }
        }

        throw new InvalidArgumentException(
            'Unable to identify a curve with private key length: ' . $length
        );
    }

    public static function curveByName(string $name) : string {
        foreach (Radix::RADIX_CURVES as $curve) {
            if ($curve::getName() === $name) {
                return $curve;
            }
        }

        throw new InvalidArgumentException(
            'Unable to identify a curve with name: ' . $name
        );
    }
}
