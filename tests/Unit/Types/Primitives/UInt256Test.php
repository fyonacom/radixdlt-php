<?php

namespace Techworker\RadixDLT\Tests\Unit\Types\Primitives;

use Techworker\RadixDLT\Tests\TestCase;
use Techworker\RadixDLT\Types\Primitives\UInt256;

class UInt256Test extends TestCase
{
    public const UINT256 = [[
        'json' => ':u20:1',
        'dson' => [
            88, 33, 5, 0, 0, 0, 0, 0, 0,
            0,  0, 0, 0, 0, 0, 0, 0, 0,
            0,  0, 0, 0, 0, 0, 0, 0, 0,
            0,  0, 0, 0, 0, 0, 0, 1,
        ],
        'str' => '1',
    ], [
        'json' => ':u20:1000000000000000000',
        'dson' => [
            88,  33,   5,   0,   0,   0, 0, 0, 0,
            0,   0,   0,   0,   0,   0, 0, 0, 0,
            0,   0,   0,   0,   0,   0, 0, 0, 0,
            13, 224, 182, 179, 167, 100, 0, 0,
        ],
        'str' => '1000000000000000000',
    ], [
        'json' => ':u20:115792089237316195423570985008687907853269984665640564039457584007913129639935',
        'dson' => [
            88,  33,   5, 255, 255, 255, 255,
            255, 255, 255, 255, 255, 255, 255,
            255, 255, 255, 255, 255, 255, 255,
            255, 255, 255, 255, 255, 255, 255,
            255, 255, 255, 255, 255, 255, 255,
        ],
        'str' => '115792089237316195423570985008687907853269984665640564039457584007913129639935',
    ]];

    public function testFromJson()
    {
        foreach (self::UINT256 as $idx => $testCase) {
            $v = UInt256::fromJson($testCase['json']);
            $this->compareToFixture($idx, $v);
        }
    }

    public function testFromDson()
    {
        foreach (self::UINT256 as $idx => $testCase) {
            $v = UInt256::fromDson($testCase['dson']);
            $this->compareToFixture($idx, $v);
        }
    }

    protected function compareToFixture(int $idx, UInt256 $uint)
    {
        $this->assertSame(self::UINT256[$idx]['json'], $uint->toJson());
        $this->assertSame(self::UINT256[$idx]['dson'], binaryToBytes((string) $uint->toDson()));
        $this->assertSame(self::UINT256[$idx]['str'], (string) $uint);
    }
}
