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

namespace Techworker\RadixDLT\Serialization;

use CBOR\AbstractCBORObject;
use CBOR\Decoder;
use CBOR\OtherObject\OtherObjectManager;
use CBOR\StringStream;
use CBOR\Tag\TagObjectManager;
use InvalidArgumentException;

class Serializer
{
    public static function primitiveToDson(array $bytes, int $prefix): string
    {
        if($prefix !== -1) {
            array_unshift($bytes, $prefix);
        }

        return bytesToBinary($bytes);
    }

    /**
     * @param array|string|AbstractCBORObject $dson
     * @return int[]
     */
    public static function primitiveFromDson(array | string | AbstractCBORObject $dson, int $checkPrefix): array
    {
        if (is_array($dson)) {
            $dson = bytesToBinary($dson);
        }

        $decoded = null;
        if ($dson instanceof AbstractCBORObject) {
            $decoded = (string) $dson->getNormalizedData();
            if ($decoded === '') {
                throw new InvalidArgumentException('Unable to decode the given DSON');
            }
        } else {
            $stream = new StringStream($dson);
            $decoder = new Decoder(new TagObjectManager(), new OtherObjectManager());
            $decoded = (string) $decoder->decode($stream)->getNormalizedData(true);
            if ($decoded === '') {
                throw new InvalidArgumentException('Unable to decode the given DSON');
            }
        }

        $bytes = binaryToBytes($decoded);
        if($checkPrefix !== -1) {
            $prefix = array_shift($bytes);
            if ($prefix !== $checkPrefix) {
                throw new InvalidArgumentException('Invalid DSON prefix.');
            }
        }

        return $bytes;
    }

    public static function primitiveFromJson(array | string $json, string $prefix): string
    {
        if (is_array($json)) {
            throw new InvalidArgumentException('The JSON Address primitive type is string.');
        }

        if (! str_starts_with($json, $prefix)) {
            throw new InvalidArgumentException('Invalid format.');
        }

        return substr($json, strlen($prefix));
    }

    /**
     * @return string|array
     */
    public static function primitiveToJson(\Stringable $instance, string $prefix): string | array
    {
        return $prefix . (string) $instance;
    }
}
