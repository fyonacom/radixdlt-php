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

namespace Techworker\RadixDLT\Types\Particles;

use Techworker\RadixDLT\Serialization\Interfaces\FromJsonInterface;
use Techworker\RadixDLT\Serialization\Interfaces\ToJsonInterface;
use Techworker\RadixDLT\Types\Core\RRI;

class RRIParticle extends AbstractParticle
{
    protected function __construct(
        protected RRI $rri
    ) {
    }

    public function getAddresses(): array
    {
        return [$this->rri];
    }

    public static function fromJson(array | string $json): self
    {
        if (is_string($json)) {
            throw new \InvalidArgumentException('Invalid.');
        }

        $rri = RRI::fromJson((string) $json['rri']);
        return new self($rri);
    }

    public function toJson(): array | string
    {
        $json = [];
        $json['rri'] = $this->rri->toJson();
        return $json;
    }
}
