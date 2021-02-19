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

use RuntimeException;

/**
 * Class KeyPair
 *
 * @psalm-consistent-constructor
 */
class KeyPair
{
    /**
     * KeyPair constructor.
     * @param PublicKey $publicKey
     * @param PrivateKey|null $privateKey
     */
    public function __construct(
        protected PublicKey $publicKey,
        protected ?PrivateKey $privateKey = null
    ) {
    }

    /**
     * Gets the private key of the pair, if available..
     */
    public function getPrivateKey(): ?PrivateKey
    {
        return $this->privateKey;
    }

    /**
     * Gets the public key of the pair.
     */
    public function getPublicKey(): PublicKey
    {
        return $this->publicKey;
    }

    /**
     * Gets the PEM representation of a keypair.
     */
    public function toPem(): string
    {
        if ($this->privateKey === null) {
            throw new RuntimeException(
                'The keypair has no private key, unable to generate PEM.'
            );
        }

        return $this->privateKey->toPem($this->publicKey);
    }
}
