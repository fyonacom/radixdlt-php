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

use Psr\Container\ContainerInterface;
use Techworker\RadixDLT\Services\KeyServiceInterface;

const RADIX_NO_VALUE = 'RADIX_NO_VALUE';

/**
 * Class Radix
 *
 * @package Techworker\RadixDLT
 */
final class Radix implements ContainerInterface
{
    public const CFG = 'radix.config';

    public const RADIX_HASH_ROUNDS = 2;

    public const RADIX_HASH_ALG = 'sha256';

    /**
     * TODO
     */
    private int $universeMagicByte = 0;

    private static Radix $instance;

    /**
     * Radix constructor.
     * @param ContainerInterface|null $outerContainer
     * @param ContainerInterface|null $innerContainer
     */
    private function __construct(
        protected ?ContainerInterface $outerContainer = null,
        protected ?ContainerInterface $innerContainer = null,
        array $config = [],
    ) {
        // TODO: merge..
        $baseConfig = include __DIR__ . '/config.php';
        $config = arrayMergeRecursiveDistinct($baseConfig, $config);

        if ($this->innerContainer === null) {
            $this->innerContainer = new Container($config);
        }
    }

    /**
     * Get the globally available instance of the container.
     *
     * @return static
     */
    public static function setup(
        ?ContainerInterface $outerContainer = null,
        ?ContainerInterface $innerContainer = null,
        array $config = []
    ): self {
        self::$instance = new self($outerContainer, $innerContainer, $config);
        return self::$instance;
    }

    public static function getInstance(): self
    {
        if (! isset(self::$instance)) {
            throw new \BadMethodCallException('Call setup first...');
        }

        return self::$instance;
    }

    /**
     * @param string $id
     */
    public function get($id): mixed
    {
        // check if the id is in any of the given containers
        if (! $this->has($id)) {
            throw new \InvalidArgumentException('Unknown container idx: ' . $id);
        }

        // first check the outer container (laravel or whatever), then the
        // internally constructed container. Container registrations from
        // the outer container take precedence
        if ($this->outerContainer !== null && $this->outerContainer->has($id)) {
            return $this->outerContainer->get($id);
        } elseif ($this->innerContainer->has($id)) {
            return $this->innerContainer->get($id);
        }
    }

    /**
     * @param string $id
     */
    public function has($id): bool
    {
        return $this->innerContainer->has($id) ||
            ($this->outerContainer !== null && $this->outerContainer->has($id));
    }

    public function keyService(): KeyServiceInterface
    {
        return $this->get(KeyServiceInterface::class);
    }

    /**
     * TODO
     */
    public function connection(): Connection
    {
        return new Connection();
    }
}
