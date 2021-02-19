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
use Techworker\RadixDLT\Services\KeyService;
use Techworker\RadixDLT\Services\KeyServiceInterface;

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
        $pimpleContainer[KeyServiceInterface::class] = fn (\Pimple\Container $c): KeyService => new KeyService(
            (array) radixConfig('crypto.keys.mapping'),
            $c[CurveResolver::class]
        );
        $pimpleContainer[OpenSSL::class] = fn (\Pimple\Container $c): OpenSSL => new OpenSSL(
            (array) radixConfig('crypto.keys', OpenSSL::class),
            $c[CurveResolver::class]
        );
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
