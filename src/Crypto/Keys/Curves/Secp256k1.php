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

namespace Techworker\RadixDLT\Crypto\Keys\Curves;

use Techworker\RadixDLT\Crypto\Keys\AbstractSimplitoEllipticKeyPair;
use Techworker\RadixDLT\Serialization\Attributes\Curve;

/**
 * Class Secp256k1
 *
 * A Secp256k1 key pair using the AbstractSimplitoEllipticPHPKeyPair library.
 */
#[Curve(name: 'secp256k1', privateKeyLengths: [32], publicKeyLengths: [33])]
class Secp256k1 extends AbstractSimplitoEllipticKeyPair
{
}
