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
use Techworker\RadixDLT\Types\BytesBased;

/**
 * Class PublicKey
 *
 * @package Techworker\RadixDLT\Crypto
 */
#[Encoding(encoding: 'hex', notSupported: ['json', 'cbor'])]
class PublicKey extends BytesBased
{
    protected string $curve;

    /**
     * PrivateKey constructor.
     * @param int[] $bytes
     */
    protected function __construct(array $bytes)
    {
        parent::__construct($bytes);
        $this->curve = CurveResolver::curveByPublicKeyLength(count($this->bytes));
    }

    /**
     * Gets the class of the curve.
     *
     * @return string
     */
    public function getCurve() : string {
        return $this->curve;
    }
}
