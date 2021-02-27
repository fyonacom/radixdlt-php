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

use Pimple\Psr11\Container as PimplePsrContainer;
use Psr\Container\ContainerInterface;
use Techworker\RadixDLT\Crypto\Keys\Adapters\OpenSSL;
use Techworker\RadixDLT\Crypto\Keys\CurveResolver;
use Techworker\RadixDLT\Serialization\ComplexSerializer;
use Techworker\RadixDLT\Serialization\PrimitiveSerializer;
use Techworker\RadixDLT\Services\KeyService;
use Techworker\RadixDLT\Services\KeyServiceInterface;
use Techworker\RadixDLT\Types\Atom;
use Techworker\RadixDLT\Types\Crypto\ECDSASignature;
use Techworker\RadixDLT\Types\Particles\Message;
use Techworker\RadixDLT\Types\Particles\ParticleGroup;
use Techworker\RadixDLT\Types\Particles\RRIParticle;
use Techworker\RadixDLT\Types\Particles\SpunParticle;
use Techworker\RadixDLT\Types\Particles\SystemParticle;
use Techworker\RadixDLT\Types\Particles\Tokens\MutableSupplyTokenDefinitionParticle;
use Techworker\RadixDLT\Types\Particles\Tokens\TransferrableTokensParticle;
use Techworker\RadixDLT\Types\Particles\Tokens\UnallocatedTokensParticle;
use Techworker\RadixDLT\Types\Universe\UniverseConfig;

/**
 * Class Container
 *
 * The library container that contains the core object instantiation
 * configuration.
 */
class Container implements ContainerInterface
{
    protected ContainerInterface $innerContainer;

    public function __construct(array $config)
    {
        $pimpleContainer = new \Pimple\Container();
        $pimpleContainer[Radix::CFG] = $config;
        $pimpleContainer[CurveResolver::class] = fn (): CurveResolver => new CurveResolver(
            (array) radixConfig('crypto.keys.supported')
        );

        $pimpleContainer[PrimitiveSerializer::class] = function (\Pimple\Container $container): PrimitiveSerializer {
            return new PrimitiveSerializer();
        };
        $pimpleContainer[ComplexSerializer::class] = function (\Pimple\Container $container): ComplexSerializer {
            return new ComplexSerializer(
                $container[PrimitiveSerializer::class]
            );
        };

        $pimpleContainer[KeyServiceInterface::class] = function (\Pimple\Container $container): KeyService {

            /** @var CurveResolver $resolver */
            $resolver = $container->offsetGet(CurveResolver::class);
            return new KeyService(
                (array) radixConfig('crypto.keys.mapping'),
                $resolver
            );
        };

        $pimpleContainer[OpenSSL::class] = function (\Pimple\Container $container): OpenSSL {
            /** @var CurveResolver $resolver */
            $resolver = $container->offsetGet(CurveResolver::class);
            return new OpenSSL(
                (array) radixConfig('crypto.keys', OpenSSL::class),
                $resolver
            );
        };

        $pimpleContainer['serialization.radix.universe'] = UniverseConfig::class;
        $pimpleContainer['serialization.radix.atom'] = Atom::class;
        $pimpleContainer['serialization.radix.particle_group'] = ParticleGroup::class;
        $pimpleContainer['serialization.radix.spun_particle'] = SpunParticle::class;
        $pimpleContainer['serialization.radix.particles.message'] = Message::class;
        $pimpleContainer['serialization.radix.particles.rri'] = RRIParticle::class;
        $pimpleContainer['serialization.radix.particles.mutable_supply_token_definition'] = MutableSupplyTokenDefinitionParticle::class;
        $pimpleContainer['serialization.radix.particles.unallocated_tokens'] = UnallocatedTokensParticle::class;
        $pimpleContainer['serialization.radix.particles.transferrable_tokens'] = TransferrableTokensParticle::class;
        $pimpleContainer['serialization.crypto.ecdsa_signature'] = ECDSASignature::class;
        $pimpleContainer['serialization.radix.particles.system_particle'] = SystemParticle::class;

        $this->innerContainer = new PimplePsrContainer($pimpleContainer);
    }

    public function get($id): mixed
    {
        return $this->innerContainer->get($id);
    }

    public function has($id): bool
    {
        return $this->innerContainer->has($id);
    }
}
