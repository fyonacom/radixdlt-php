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

// TODO!
use Techworker\RadixDLT\Connection\Api;

class Connection
{
    protected int $universeMagicByte = 0;

    public function __construct(
        protected ?string $wsUri,
        protected ?string $rpcUri,
        protected ?string $apiUri)
    {
    }

    public function getUniverseMagicByte(): int
    {
        return $this->universeMagicByte;
    }

    public function api() : Api {
        return new Api($this, $this->apiUri);
    }
}
