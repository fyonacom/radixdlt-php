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

use Techworker\RadixDLT\Serialization\Attributes\Curve;

/**
 * Class KeyPair
 *
 * @psalm-consistent-constructor
 */
abstract class AbstractKeyPair implements CurveInterface
{
    /**
     * KeyPair constructor.
     * @param PublicKey $publicKey
     * @param PrivateKey|null $privateKey
     */
    public function __construct(protected PublicKey $publicKey,
                                protected ?PrivateKey $privateKey = null)
    {
    }

    /**
     * Gets the name of the curve.
     *
     * @return string
     */
    public static function getName() : string {
        return (string)Curve::getParam('name', static::class);
    }

    /**
     * The possible lengths of the private key.
     *
     * @return array
     */
    public static function getPrivateKeyLengths() : array {
        return (array)Curve::getParam('privateKeyLengths', static::class);
    }

    /**
     * The possible lengths of the public key.
     *
     * @return array
     */
    public static function getPublicKeyLengths() : array {
        return (array)Curve::getParam('publicKeyLengths', static::class);
    }

    /**
     * Gets the private key of the pair, if available..
     *
     * @return PrivateKey|null
     */
    public function getPrivateKey(): ?PrivateKey
    {
        return $this->privateKey;
    }

    /**
     * Gets the public key of the pair.
     *
     * @return PublicKey
     */
    public function getPublicKey(): PublicKey
    {
        return $this->publicKey;
    }
}
