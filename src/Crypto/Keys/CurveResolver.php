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

namespace Techworker\RadixDLT\Crypto\Keys;

use InvalidArgumentException;

/**
 * Class CurveResolver
 *
 * A simple helper class that tries to identify a curve by various parameters.
 */
class CurveResolver
{
    /**
     * CurveResolver constructor.
     * @param array $supportedCurves
     */
    public function __construct(
        protected array $supportedCurves
    ) {
    }

    /**
     * Tries to determine a curve by the given public key length. Returns the
     * name of the curve class.
     *
     * @throws InvalidArgumentException
     */
    public function byPublicKeyLength(int $length): string
    {
        /** @var AbstractCurve $curve */
        foreach ($this->supportedCurves as $curve) {
            if (in_array($length, $curve::getPublicKeyLengths(), true)) {
                /** @var string $curve */
                return $curve;
            }
        }

        throw new InvalidArgumentException(
            'Unable to identify a curve with public key length: ' . $length
        );
    }

    /**
     * Tries to determine a curve by the given public key length. Returns the
     * name of the curve class.
     *
     * @throws InvalidArgumentException
     */
    public function byPrivateKeyLength(int $length): string
    {
        /** @var AbstractCurve $curve */
        foreach ($this->supportedCurves as $curve) {
            if (in_array($length, $curve::getPrivateKeyLengths(), true)) {
                /** @var string $curve */
                return $curve;
            }
        }

        throw new InvalidArgumentException(
            'Unable to identify a curve with private key length: ' . $length
        );
    }

    /**
     * Tries to determine the a curve by the given name. Returns the
     * name of the curve class.
     */
    public function byName(string $name): string
    {
        if (class_exists($name)) {
            return $name;
        }

        /** @var AbstractCurve $curve */
        foreach ($this->supportedCurves as $curve) {
            if ($curve::getName() === $name) {
                /** @var string $curve */
                return $curve;
            }
        }

        throw new InvalidArgumentException(
            'Unable to identify a curve with name: ' . $name
        );
    }
}
