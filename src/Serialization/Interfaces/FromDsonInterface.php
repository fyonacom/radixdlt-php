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

namespace Techworker\RadixDLT\Serialization\Interfaces;

use CBOR\CBORObject;

interface FromDsonInterface
{
    /**
     * Tries to return a new instance of the implementing class from the given dson.
     *
     * @param array|string|CBORObject $dson
     * @return static
     */
    public static function fromDson(array | string | CBORObject $dson, string $enc = 'bin'): static;
}
