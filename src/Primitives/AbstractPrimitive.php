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

namespace Techworker\RadixDLT\Primitives;

use CBOR\AbstractCBORObject;
use Techworker\RadixDLT\Serialization\Attributes\CBOR;
use Techworker\RadixDLT\Serialization\Attributes\DefaultEncoding;
use Techworker\RadixDLT\Serialization\Attributes\JsonPrefix;
use function Techworker\RadixDLT\bytesToEnc;
use function Techworker\RadixDLT\bytesToString;

abstract class AbstractPrimitive
{
    /**
     * AbstractPrimitive constructor.
     * @param array $bytes
     */
    public function __construct(protected array $bytes)
    {
    }

    public function cbor(): AbstractCBORObject
    {
        $bytes = $this->to('bytes');
        array_unshift($bytes, CBOR::getParam('prefix', static::class));
        $type = CBOR::getParam('target', static::class);
        return new $type(bytesToString($bytes));
    }

    public function to(string $enc = 'hex'): array|string
    {
        return match ($enc) {
            'array', 'bytes' => $this->bytes,
            'json' => JsonPrefix::getParam('prefix', static::class) . $this->to('base58'),
            'cbor' => $this->cbor(),
            default => bytesToEnc($this->bytes, $enc),
        };
    }

    abstract public static function from(string|array $data, string $enc = null);

    public function __toString(): string
    {
        return $this->to(DefaultEncoding::getParam('encoding', static::class));
    }

    protected static function stripJsonPrefix(string $data) {
        return substr($data, strlen(JsonPrefix::getParam('prefix', static::class)));
    }
}
