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

/**
 * Interface AdapterInterface
 * @package Techworker\RadixDLT\Crypto\Keys
 */
interface AdapterInterface
{
    /**
     * Tries to create a new KeyPair from the given private key.
     *
     * @param string|int[]|PrivateKey $privateKey
     */
    public function fromPrivateKey(string | array | PrivateKey $privateKey, ?string $enc = 'hex'): KeyPair;

    /**
     * Tries to create a new KeyPair from the given public key.
     *
     * @param string|int[]|PublicKey $publicKey
     */
    public function fromPublicKey(string | array | PublicKey $publicKey, ?string $enc = 'hex'): KeyPair;

    /**
     * Generates a new keypair based on the given curve.
     */
    public function generateNew(string $curve): KeyPair;
}
