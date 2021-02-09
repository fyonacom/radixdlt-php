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

namespace Techworker\RadixDLT\Types\Core;

use CBOR\ByteStringObject;
use InvalidArgumentException;
use Techworker\RadixDLT\Crypto\Keys\AbstractKeyPair;
use Techworker\RadixDLT\Crypto\Keys\CurveResolver;
use Techworker\RadixDLT\Crypto\Keys\Curves\Secp256k1;
use Techworker\RadixDLT\Crypto\Keys\PrivateKey;
use Techworker\RadixDLT\Crypto\Keys\PublicKey;
use Techworker\RadixDLT\Serialization\Attributes\CBOR;
use Techworker\RadixDLT\Serialization\Attributes\Encoding;
use Techworker\RadixDLT\Serialization\Attributes\Json;
use Techworker\RadixDLT\Types\BytesBased;
use function Techworker\RadixDLT\bytesToEnc;
use function Techworker\RadixDLT\encToBytes;
use function Techworker\RadixDLT\radixHash;

/**
 * Class RadixString
 */
#[Json(prefix: ':str:', encoding: 'bin')]
#[CBOR(prefix: 4, target: ByteStringObject::class)]
#[Encoding(encoding: 'bin')]
class String_ extends BytesBased
{
}
