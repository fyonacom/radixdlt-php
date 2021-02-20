<?php

namespace Techworker\RadixDLT\Tests\Unit\Types\Primitives;

use Techworker\RadixDLT\Tests\TestCase;
use Techworker\RadixDLT\Types\Primitives\UID;

class UIDTest extends TestCase
{
    public const UID = [
        'json' => ':uid:0c767ab918b29333ee48554006f261de',
        'dson' => [
            81,   2,  12, 118, 122, 185,
            24, 178, 147,  51, 238,  72,
            85,  64,   6, 242,  97, 222,
        ],
        'str' => '0c767ab918b29333ee48554006f261de',
        'shard_hex' => '0c767ab918b29333',
        'shard_uint128' => '898040111108887347',
    ];

    public function testFromJson()
    {
        $uid = UID::fromJson(self::UID['json']);
        $this->compareToFixture($uid);
    }

    public function testFromDson()
    {
        $uid = UID::fromDson(self::UID['dson']);
        $this->compareToFixture($uid);
    }

    public function testBadLength()
    {
        $this->expectException(\InvalidArgumentException::class);
        new UID([1, 2, 3]);
    }

    protected function compareToFixture(UID $uid)
    {
        $this->assertSame(self::UID['json'], $uid->toJson());
        $this->assertSame(self::UID['dson'], binaryToBytes((string) $uid->toDson()));
        $this->assertSame(self::UID['str'], (string) $uid);
        $this->assertSame(self::UID['shard_hex'], $uid->getShard()->toString(16));
        $this->assertSame(self::UID['shard_uint128'], $uid->getShard()->toString(10));
    }
}
