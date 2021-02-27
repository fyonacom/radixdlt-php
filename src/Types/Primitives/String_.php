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

namespace Techworker\RadixDLT\Types\Primitives;

use Techworker\RadixDLT\Serialization\Attributes\Dson;
use Techworker\RadixDLT\Serialization\Attributes\Encoding;
use Techworker\RadixDLT\Serialization\Attributes\JsonPrimitive;
use Techworker\RadixDLT\Serialization\EncodingType;
use Techworker\RadixDLT\Types\Primitive;

/**
 * Class String_
 * @package Techworker\RadixDLT\Types\Primitives
 */
#[Dson(majorType: 3, prefix: null)]
#[JsonPrimitive(prefix: ':str:')]
#[Encoding(encoding: EncodingType::STR)]
class String_ extends Primitive
{
}
