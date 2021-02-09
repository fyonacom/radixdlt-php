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

class ParticleGroup
    implements ToJsonInterface, FromJsonInterface
{
    /**
     * RadixParticleGroup constructor.
     * @param SpunParticle[] $particles
     * @param array $metaData
     */
    public function __construct(protected array $particles = [], protected array $metaData)
    {
    }

    public static function fromJson(array|string $json): self
    {
        if(is_string($json)) {
            throw new \Exception('...');
        }

        $spunParticles = [];
        /** @var array $jsonParticle */
        foreach((array)$json['particles'] as $jsonParticle) {
            $spunParticles[] = SpunParticle::fromJson($jsonParticle);
        }

        return new self($spunParticles, []);
    }

    public function toJson(): array|string
    {
        $json = [];
        $json['particles'] = [];
        $json['metaData'] = [];
        $json['serializer'] = 'radix.particle_group';
        foreach($this->particles as $particle) {
            $json['particles'][] = $particle->toJson();
        }
        return $json;
    }


}
