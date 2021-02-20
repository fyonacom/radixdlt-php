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

/**
 * Class AbstractCurve
 */
abstract class AbstractCurve
{
    /**
     * Gets the name of the curve.
     */
    abstract public static function getName(): string;

    /**
     * The possible lengths of the private key.
     *
     * @return int[]
     */
    abstract public static function getPrivateKeyLengths(): array;

    /**
     * The possible lengths of the public key.
     *
     * @return int[]
     */
    abstract public static function getPublicKeyLengths(): array;
}
