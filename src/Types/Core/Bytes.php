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
use Techworker\RadixDLT\Serialization\Attributes\CBOR;
use Techworker\RadixDLT\Serialization\Attributes\Encoding;
use Techworker\RadixDLT\Serialization\Attributes\Json;
use Techworker\RadixDLT\Types\BytesBased;
use function Techworker\RadixDLT\bytesToEnc;
use function Techworker\RadixDLT\encToBytes;
use function Techworker\RadixDLT\writeUInt32BE;

/**
 * Class RadixUID
 *
 * @package Techworker\RadixDLT
 */
#[Json(prefix: ':byt:', encoding: 'base64')]
#[CBOR(prefix: 1, target: ByteStringObject::class)]
#[Encoding(encoding: 'base64')]
class Bytes extends BytesBased
{
}
