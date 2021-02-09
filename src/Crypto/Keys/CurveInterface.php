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

use Techworker\RadixDLT\Serialization\Attributes\Encoding;

/**
 * Interface CurveInterface
 * @package Techworker\RadixDLT\Crypto
 */
interface CurveInterface
{
    /**
     * Generates a new KeyPair.
     *
     * @return static
     */
    public static function generateNew() : static;

    /**
     * Returns a new keypair based on the given public key.
     *
     * @param string|array|PublicKey $publicKey
     * @param string|null $enc
     * @return static
     */
    public static function fromPublicKey(string|array|PublicKey $publicKey, ?string $enc = 'hex'): static;

    /**
     * Returns a new keypair based on the given private key.
     *
     * @param string|array|PrivateKey $privateKey
     * @param string|null $enc
     * @return static
     */
    public static function fromPrivateKey(string|array|PrivateKey $privateKey, ?string $enc = 'hex'): static;
}
