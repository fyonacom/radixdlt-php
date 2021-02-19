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

interface ToJsonInterface
{
    /**
     * Tries to return a new json representation of the implementing class.
     *
     * @return array|string
     */
    public function toJson(): array | string;
}
