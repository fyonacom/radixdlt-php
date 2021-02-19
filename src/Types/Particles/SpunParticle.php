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

/**
 * Class RadixSpunParticle
 * @package Techworker\RadixDLT\Types\Particles
 */
class SpunParticle
{
    public const SERIALIZER = 'radix.spun_particle';

    public const SPIN_UP = 1;

    public const SPIN_NEUTRAL = 0;

    public const SPIN_DOWN = -1;

    protected function __construct(
        protected AbstractParticle $particle,
        protected int $spin
    ) {
    }

    public function up(AbstractParticle $particle): self
    {
        return new self($particle, self::SPIN_UP);
    }

    public function down(AbstractParticle $particle): self
    {
        return new self($particle, self::SPIN_DOWN);
    }

    public static function fromJson(array | string $json): self
    {
        if (is_string($json)) {
            throw new \InvalidArgumentException('Invalid.');
        }

        /** @var AbstractParticle $particle */
        $particle = AbstractParticle::fromJson((array) $json['particle']);
        return new self($particle, (int) $json['spin']);
    }

    public function toJson(): array | string
    {
        $json = [];
        $json['serializer'] = self::SERIALIZER;
        $json['particle'] = $this->particle->toJson();
        $json['spin'] = $this->spin;
        return $json;
    }
}
