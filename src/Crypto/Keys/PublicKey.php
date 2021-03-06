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
 * Class PublicKey
 *
 * @package Techworker\RadixDLT\Crypto
 */
class PublicKey
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
        $this->curve = $curveResolver->byPublicKeyLength($this->countBytes());

        /** @var AbstractCurve $curve */
        $curve = $this->curve;

        $diffExpectedLength = $curve::getPublicKeyLengths()[0] - $this->countBytes();
        // https://stackoverflow.com/questions/62938091/why-are-secp256k1-privatekeys-not-always-32-bytes-in-nodejs
        if ($diffExpectedLength > 0) {
            $new = [];
            // marker
            $new[] = $this->bytes[0];
            // upfill
            array_push($new, ...array_fill(0, $diffExpectedLength, 0));
            // rest
            array_push($new, ...$this->slice(1));
            $this->initBytes($new);
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
}
