<?php

namespace Techworker\RadixDLT\Tests\Unit\Types\Primitives;

use Techworker\RadixDLT\Tests\TestCase;
use Techworker\RadixDLT\Types\Primitives\AID;

class AIDTest extends TestCase
{
    public const AID = [
        'json' => ':aid:e60e9a3e644be6f44680b220bd611b3f0b635adbfe9a6f83ac089715a77cd581',
        'dson' => [
            88,  33,   8, 230,  14, 154,  62, 100,
            75, 230, 244,  70, 128, 178,  32, 189,
            97,  27,  63,  11,  99,  90, 219, 254,
            154, 111, 131, 172,   8, 151,  21, 167,
            124, 213, 129,
        ],
        'str' => 'e60e9a3e644be6f44680b220bd611b3f0b635adbfe9a6f83ac089715a77cd581',
    ];

    public function testFromJson()
    {
        $aid = AID::fromJson(self::AID['json']);
        $this->compareToFixture($aid);
    }

    public function testFromDson()
    {
        $aid = AID::fromDson(self::AID['dson']);
        $this->compareToFixture($aid);
    }

    public function testThrowsExceptionWithBadLength()
    {
        $this->expectException(\InvalidArgumentException::class);
        AID::fromJson(substr(self::AID['json'], 0, -2));
    }

    protected function compareToFixture(AID $aid)
    {
        $this->assertSame(self::AID['json'], $aid->toJson());
        $this->assertSame(self::AID['dson'], binaryToBytes((string) $aid->toDson()));
        $this->assertSame(self::AID['str'], (string) $aid);
    }
}
