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

use BN\BN;
use CBOR\AbstractCBORObject;
use CBOR\InfiniteMapObject;
use CBOR\TextStringObject;
use CBOR\UnsignedIntegerObject;
use Techworker\RadixDLT\Serialization\Interfaces\FromJsonInterface;
use Techworker\RadixDLT\Serialization\Interfaces\ToDsonInterface;

class SystemParticle extends AbstractParticle implements FromJsonInterface, ToDsonInterface
{
    protected function __construct(
        protected int $epoch,
        protected int $view,
        protected int $timestamp
    ) {
    }

    public static function fromJson(array|string $json): static
    {
        if (is_string($json)) {
            throw new \InvalidArgumentException('Invalid.');
        }

        $data = [
            'epoch' => (int)$json['epoch'],
            'view' => (int)$json['view'],
            'timestamp' => (int)$json['timestamp']
        ];
        return new SystemParticle(...$data);
    }

    public function toDson(): AbstractCBORObject
    {
        $mo = new InfiniteMapObject();
        $mo->append(new TextStringObject('epoch'), UnsignedIntegerObject::create($this->epoch));
        $mo->append(new TextStringObject('serializer'), new TextStringObject('radix.particles.system_particle'));

        // its a millisecond timestamp thats > 32bit, so we need to apply a small trick
        // to get it to work, otherwise the cbor library will throw an error
        $ts = hexToString((string)(new BN((string)$this->timestamp))->toString(16, 8));
        $mo->append(new TextStringObject('timestamp'),
                    UnsignedIntegerObject::createObjectForValue(27, $ts));
        $mo->append(new TextStringObject('view'), UnsignedIntegerObject::create(1000));
        return $mo;
    }
}
