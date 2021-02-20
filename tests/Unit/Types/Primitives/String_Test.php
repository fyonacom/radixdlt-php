<?php

namespace Techworker\RadixDLT\Tests\Unit\Types\Primitives;

use Techworker\RadixDLT\Tests\TestCase;
use Techworker\RadixDLT\Types\Primitives\AID;
use Techworker\RadixDLT\Types\Primitives\String_;

class String_Test extends TestCase
{
    public const STR = [
        'json' => ':str:techworker',
        'dson' => [
            106, 116, 101,  99,
            104, 119, 111, 114,
            107, 101, 114
        ],
        'str' => 'techworker',
    ];

    public function testFromJson()
    {
        $str = String_::fromJson(self::STR['json']);
        $this->compareToFixture($str);
    }

    public function testFromDson()
    {
        $str = String_::fromDson(self::STR['dson']);
        $this->compareToFixture($str);
    }

    protected function compareToFixture(String_ $str)
    {
        $this->assertSame(self::STR['json'], $str->toJson());
        $this->assertSame(self::STR['dson'], binaryToBytes((string) $str->toDson()));
        $this->assertSame(self::STR['str'], (string) $str);
    }
}
