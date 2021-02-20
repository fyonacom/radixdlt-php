<?php

namespace Techworker\RadixDLT\Tests\Unit\Types\Primitives;

use Techworker\RadixDLT\Tests\TestCase;
use Techworker\RadixDLT\Types\Primitives\Hash;
use Techworker\RadixDLT\Types\Primitives\UID;

class HashTest extends TestCase
{
    public const HASH = [
        'json' => ':hsh:2db651a7eaa6d5ac8fd3d8ea7a294778bfe7a53c135524e2cf3bdc6f640dbf2c',
        'dson' => [
            88,  33,   3,  45, 182,  81, 167, 234,
            166, 213, 172, 143, 211, 216, 234, 122,
            41,  71, 120, 191, 231, 165,  60,  19,
            85,  36, 226, 207,  59, 220, 111, 100,
            13, 191,  44,
        ],
        'str' => '2db651a7eaa6d5ac8fd3d8ea7a294778bfe7a53c135524e2cf3bdc6f640dbf2c',
    ];

    public function testFromJson()
    {
        $hash = Hash::fromJson(self::HASH['json']);
        $this->compareToFixture($hash);
    }

    public function testFromDson()
    {
        $hash = Hash::fromDson(self::HASH['dson']);
        $this->compareToFixture($hash);
    }

    public function testBadLength()
    {
        $this->expectException(\InvalidArgumentException::class);
        new Hash([1,2,3]);
    }

    protected function compareToFixture(Hash $hash)
    {
        $this->assertSame(self::HASH['json'], $hash->toJson());
        $this->assertSame(self::HASH['dson'], binaryToBytes((string) $hash->toDson()));
        $this->assertSame(self::HASH['str'], (string) $hash);
    }
}
