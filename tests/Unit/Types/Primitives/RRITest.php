<?php

namespace Techworker\RadixDLT\Tests\Unit\Types\Primitives;

use Techworker\RadixDLT\Tests\TestCase;
use Techworker\RadixDLT\Types\Primitives\Address;
use Techworker\RadixDLT\Types\Primitives\RRI;

class RRITest extends TestCase
{
    public const RRI = [
        'json' => ':rri:/JH3BuQw985MrbEdrNvW9ixG7evay2rgAhVTppaUkvayJ2r1WszP/XRD',
        'dson' => [
            88,  57,   6,  47,  74,  72,  51,  66, 117,  81, 119,
            57,  56,  53,  77, 114,  98,  69, 100, 114,  78, 118,
            87,  57, 105, 120,  71,  55, 101, 118,  97, 121,  50,
            114, 103,  65, 104,  86,  84, 112, 112,  97,  85, 107,
            118,  97, 121,  74,  50, 114,  49,  87, 115, 122,  80,
            47,  88,  82,  68,
        ],
        'str' => '/JH3BuQw985MrbEdrNvW9ixG7evay2rgAhVTppaUkvayJ2r1WszP/XRD',
        'symbol' => 'XRD',
        'address' => 'JH3BuQw985MrbEdrNvW9ixG7evay2rgAhVTppaUkvayJ2r1WszP',
    ];

    public function testFromJson()
    {
        $rri = RRI::fromJson(self::RRI['json']);
        $this->compareToFixture($rri);
    }

    public function testBadRRI()
    {
        $this->expectException(\InvalidArgumentException::class);
        RRI::fromJson(':rri:JH3BuQw985MrbEdrNvW9ixG7evay2rgAhVTppaUkvayJ2r1WszP|XRD');
    }

    public function testFromDson()
    {
        $rri = RRI::fromDson(self::RRI['dson']);
        $this->compareToFixture($rri);
    }

    public function testFromAddressAndSymbol()
    {
        $rri = RRI::fromAddressAndSymbol(
            Address::fromJson(':adr:' . self::RRI['address']),
            self::RRI['symbol']
        );
        $this->compareToFixture($rri);
    }

    protected function compareToFixture(RRI $rri)
    {
        $this->assertSame(self::RRI['json'], $rri->toJson());
        $this->assertSame(self::RRI['dson'], binaryToBytes((string) $rri->toDson()));
        $this->assertSame(self::RRI['str'], (string) $rri);
        $this->assertSame(self::RRI['symbol'], (string) $rri->getSymbol());
        $this->assertInstanceOf(Address::class, $rri->getAddress());
        $this->assertSame(self::RRI['address'], (string) $rri->getAddress());
    }
}
