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

use function Techworker\RadixDLT\bytesToEnc;
use function Techworker\RadixDLT\encToBytes;

class PublicKey
{
    protected array $bytes;

    public function __construct(protected string $curve,
                                array|string $data,
                                string $enc = 'hex')
    {
        if(is_string($data)) {
            $this->bytes = encToBytes($data, $enc);
        } elseif(is_array($data)) {
            $this->bytes = $data;
        }

        // TODO: length check
    }

    public static function from(array|string $data, string $enc = 'hex') : PublicKey {
        $length = 0;
        if(is_array($data)) {
            $length = count($data);
        } elseif (is_string($data)) {
            $length = count(encToBytes($data, $enc));
        }

        $curve = CurveInfo::curveByPublicKeyLength($length);
        return new PublicKey($curve, $data, $enc);
    }

    public function getCurve() : string {
        return $this->curve;
    }

    public function to(string $enc = 'hex'): array|string
    {
        return match ($enc) {
            'array', 'bytes' => $this->bytes,
            default => bytesToEnc($this->bytes, $enc),
        };
    }

    public function __toString(): string
    {
        return $this->to('hex');
    }
}
