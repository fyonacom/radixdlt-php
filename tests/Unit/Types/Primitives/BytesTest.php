<?php

namespace Techworker\RadixDLT\Tests\Unit\Types\Primitives;

use Techworker\RadixDLT\Tests\TestCase;
use Techworker\RadixDLT\Types\Primitives\Bytes;

class BytesTest extends TestCase
{
    public const BYTES = [
        'json' => ':byt:4eWPInk2blDO6+3z2D1pGj71zDkS0i/wi8gq0s94jd0=',
        'dson' => [
            88,  33,   1, 225, 229, 143,  34, 121,
            54, 110,  80, 206, 235, 237, 243, 216,
            61, 105,  26,  62, 245, 204,  57,  18,
            210,  47, 240, 139, 200,  42, 210, 207,
            120, 141, 221,
        ],
        'str' => '4eWPInk2blDO6+3z2D1pGj71zDkS0i/wi8gq0s94jd0=',
    ];

    public function testFromJson()
    {
        $bytes = Bytes::fromJson(self::BYTES['json']);
        $this->compareToFixture($bytes);
    }

    public function testFromDson()
    {
        $bytes = Bytes::fromDson(self::BYTES['dson']);
        $this->compareToFixture($bytes);
    }

    protected function compareToFixture(Bytes $bytes)
    {
        $this->assertSame(self::BYTES['json'], $bytes->toJson());
        $this->assertSame(self::BYTES['dson'], binaryToBytes((string) $bytes->toDson()));
        $this->assertSame(self::BYTES['str'], (string) $bytes);
    }
}
