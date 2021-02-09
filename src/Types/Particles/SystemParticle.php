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

use Techworker\RadixDLT\Serialization\FromJsonInterface;
use Techworker\RadixDLT\Serialization\ToJsonInterface;
use Techworker\RadixDLT\Types\Core\Address;
use Techworker\RadixDLT\Types\Core\RRI;

class SystemParticle extends AbstractParticle
    implements ToJsonInterface, FromJsonInterface
{

    protected function __construct(
        protected int $epoch,
        protected int $view,
        protected int $timestamp
    ) {
    }

    public function getAddresses(): array
    {
        return [];
    }

    public static function fromJson(array|string $json): SystemParticle
    {
        if(is_string($json)) {
            throw new \InvalidArgumentException('Invalid.');
        }

        $epoch = (int)$json['epoch'];
        $view = (int)$json['view'];
        $timestamp = (int)$json['timestamp'];
        return new self($epoch, $view, $timestamp);
    }

    public function toJson(): array|string
    {
        $json = [];
        $json['epoch'] = $this->epoch;
        $json['view'] = $this->view;
        $json['timestamp'] = $this->timestamp;
        return $json;
    }
}
