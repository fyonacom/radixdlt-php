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

use Techworker\RadixDLT\Types\BytesTrait;

/**
 * Class PrivateKey
 *
 * @package Techworker\RadixDLT\Crypto
 */
class PrivateKey
{
    use BytesTrait;

    protected string $curve;

    /**
     * PrivateKey constructor.
     * @param int[] $bytes
     */
    protected function __construct(array $bytes)
    {
        $this->initBytes($bytes);

        /** @var CurveResolver $curveResolver */
        $curveResolver = radix()->get(CurveResolver::class);
        $this->curve = $curveResolver->byPrivateKeyLength(count($this->bytes));

        /** @var AbstractCurve $curve */
        $curve = $this->curve;
        $diffExpectedLength = $curve::getPrivateKeyLengths()[0] - $this->countBytes();
        // https://stackoverflow.com/questions/62938091/why-are-secp256k1-privatekeys-not-always-32-bytes-in-nodejs
        if ($diffExpectedLength > 0) {
            array_unshift($this->bytes, ...array_fill(0, $diffExpectedLength, 0));
        }
    }

    public function __toString(): string
    {
        return $this->toHex();
    }

    /**
     * Gets the class of the curve.
     */
    public function getCurve(): string
    {
        return $this->curve;
    }

    /**
     * Gets the PEM representation.
     */
    public function toPem(PublicKey $publicKey = null): string
    {
        $bytes = $this->bytes;

        // https://bitcoin.stackexchange.com/questions/66594/signing-transaction-with-ssl-private-key-to-pem
        array_unshift($bytes, ...hexToBytes('302e0201010420'));
        array_push($bytes, ...hexToBytes('a00706052b8104000a'));
        if ($publicKey !== null) {
            array_push($bytes, ...$publicKey->bytes);
        }

        $pem = chunk_split(bytesToBase64($bytes), 64);
        return "-----BEGIN EC PRIVATE KEY-----\n" . $pem . "-----END EC PRIVATE KEY-----\n";
    }

    /**
     * Gets the DER representation.
     */
    public function toDer(): string
    {
        return $this->toBinary();
    }
}
