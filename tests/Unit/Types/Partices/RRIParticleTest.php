<?php

namespace Techworker\RadixDLT\Tests\Unit\Types\Particles;

use Techworker\RadixDLT\Crypto\Keys\Curves\Secp256k1;
use Techworker\RadixDLT\Crypto\Keys\KeyPair;
use Techworker\RadixDLT\Crypto\Keys\PrivateKey;
use Techworker\RadixDLT\Crypto\Keys\PublicKey;
use Techworker\RadixDLT\Tests\TestCase;
use Techworker\RadixDLT\Types\Particles\RRIParticle;
use Techworker\RadixDLT\Types\Primitives\Address;

class RRIParticleTest extends TestCase
{

    public const PARTICLE = [
        'rri' => '/23B6fH3FekJeP6e5guhZAk6n9z4fmTo5Tngo3a11Wg5R8gsWTV2x/COOKIE',
        'rri_json' => ':rri:/23B6fH3FekJeP6e5guhZAk6n9z4fmTo5Tngo3a11Wg5R8gsWTV2x/COOKIE',
        'nonce' => 0,
        'dson' => 'bf656e6f6e63650063727269583d062f3233423666483346656b4a65503665356775685a416b366e397a34666d546f35546e676f336131315767355238677357545632782f434f4f4b49456a73657269616c697a65727372616469782e7061727469636c65732e727269ff',
        'serializer' => RRIParticle::SERIALIZER
    ];

    public function testFromJson()
    {
        $particle = RRIParticle::fromJson([
            'serializer' => self::PARTICLE['serializer'],
            'rri' => self::PARTICLE['rri_json'],
            'nonce' => self::PARTICLE['nonce']
        ]);
        $this->compareWithFixture($particle);
    }

    public function testFromDson()
    {
        $particle = RRIParticle::fromDson(self::PARTICLE['dson'], 'hex');
        $this->compareWithFixture($particle);
    }

    public function testToDson()
    {
        $particle = RRIParticle::fromDson(self::PARTICLE['dson'], 'hex');
        $this->assertEquals(self::PARTICLE['dson'], stringToHex($particle->toDson()));
    }

    protected function compareWithFixture(RRIParticle $particle) {
        $this->assertEquals(self::PARTICLE['rri'], (string)$particle->getRri());
        $this->assertEquals(self::PARTICLE['nonce'], (string)$particle->getNonce());
    }
}
