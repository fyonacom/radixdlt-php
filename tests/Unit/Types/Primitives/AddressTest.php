<?php

namespace Techworker\RadixDLT\Tests\Unit\Types\Primitives;

use Techworker\RadixDLT\Crypto\Keys\Curves\Secp256k1;
use Techworker\RadixDLT\Crypto\Keys\KeyPair;
use Techworker\RadixDLT\Crypto\Keys\PrivateKey;
use Techworker\RadixDLT\Crypto\Keys\PublicKey;
use Techworker\RadixDLT\Tests\TestCase;
use Techworker\RadixDLT\Types\Primitives\Address;

class AddressTest extends TestCase
{
    public const ADDRESS = [
        'json' => ':adr:k1etbta7tKxA1EsL9kLW3Kf37ACdrjvkR5pDCgL9UfcYMsR1a7B',
        'dson' => [
            88,  39,  4,   5,   3,  88,  24, 73, 206, 101,
            91, 139, 36,  16,  89, 247, 228,  1, 125,   9,
            132, 173, 62,  37,  89, 130, 152, 48,  58,   9,
            236, 122, 73, 221, 250, 237,  74, 71,  11,   3,
            74,
        ],
        'str' => 'k1etbta7tKxA1EsL9kLW3Kf37ACdrjvkR5pDCgL9UfcYMsR1a7B',
        'publicKey' => '03581849ce655b8b241059f7e4017d0984ad3e25598298303a09ec7a49ddfaed4a',
        'privateKey' => '892e1de1cd0e3ffd56152ff6cea77875ef8056b34187ba0ef089993b1cd12728',
        'hash' => '1d041f0dc3211ed00fbbfaa5cdfed7b5f3e934fca46b56d8412771e29ed2edbb',
        'shard' => '1d041f0dc3211ed0',
        'uid' => '1d041f0dc3211ed00fbbfaa5cdfed7b5',
        'universe' => 5,
    ];

    public function testFromJson()
    {
        $json = self::ADDRESS['json'];
        $address = Address::fromJson($json);
        $this->compareToFixture($address);
    }

    public function testFromJsonArrayThrowsException()
    {
        $this->expectException(\InvalidArgumentException::class);
        Address::fromJson([]);
    }

    public function testFromDson()
    {
        $dson = self::ADDRESS['dson'];
        $address = Address::fromDson($dson);
        $this->compareToFixture($address);
    }

    public function testToDson()
    {
        $address = Address::fromDson(self::ADDRESS['dson']);
        $this->assertSame(bytesToBinary(self::ADDRESS['dson']), (string) $address->toDson());
    }

    public function testInvalidChecksumThrowsException()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid checksum for address');
        $address = base58ToBytes(self::ADDRESS['str']);
        ++$address[count($address) - 1];
        Address::fromBytes($address);
    }

    public function testToStringReturnsBase58()
    {
        $address = Address::fromDson(self::ADDRESS['dson']);
        $this->assertSame(self::ADDRESS['str'], (string) $address);
    }

    public function testCanGenerateANewAddressWithUniverse()
    {
        $address = Address::generateNew(Secp256k1::class, 5);
        $this->assertSame(5, $address->getUniverseMagicByte());
    }

    public function testCanGenerateANewAddressWithoutUniverse()
    {
        $address = Address::generateNew(Secp256k1::class);
        // TODO: once the magic byte is from the connection this might fail
        $this->assertSame(0, $address->getUniverseMagicByte());
    }

    public function testFromKeyPairWithUniverse()
    {
        $kp = new KeyPair(
            PublicKey::fromHex(self::ADDRESS['publicKey']),
            PrivateKey::fromHex(self::ADDRESS['privateKey'])
        );
        $address = Address::fromKeyPair($kp, 5);
        $this->compareToFixture($address);
    }

    public function testFromKeyPairWithoutUniverse()
    {
        $kp = new KeyPair(
            PublicKey::fromHex(self::ADDRESS['publicKey']),
            PrivateKey::fromHex(self::ADDRESS['privateKey'])
        );
        $address = Address::fromKeyPair($kp);
        $this->assertSame(0, $address->getUniverseMagicByte());
        $this->assertSame($kp->getPublicKey()->toHex(), $address->getPublicKey()->toHex());
        $this->assertSame($kp->getPrivateKey()->toHex(), $address->getPrivateKey()->toHex());
        // we cannot compare with the fixture here, a different universe
        // changes the whole address
    }

    public function testFromPublicKeyWithUniverse()
    {
        $address = Address::fromPublicKey(PublicKey::fromHex(self::ADDRESS['publicKey']), 5);
        $this->compareToFixture($address);
    }

    public function testFromPublicKeyWithoutUniverse()
    {
        $address = Address::fromPublicKey(PublicKey::fromHex(self::ADDRESS['publicKey']));
        $this->assertSame(0, $address->getUniverseMagicByte());
        $this->assertSame(self::ADDRESS['publicKey'], $address->getPublicKey()->toHex());
        $this->assertNull($address->getPrivateKey());
    }

    public function testFromPrivateKeyWithUniverse()
    {
        $address = Address::fromPrivateKey(PrivateKey::fromHex(self::ADDRESS['privateKey']), 5);
        $this->compareToFixture($address);
    }

    public function testFromHexEncodedPrivateKey()
    {
        $address = Address::fromPrivateKey(PrivateKey::fromHex(self::ADDRESS['privateKey']), 5, 'hex');
        $this->compareToFixture($address);
    }

    public function testFromPrivateKeyWithoutUniverse()
    {
        $address = Address::fromPrivateKey(PrivateKey::fromHex(self::ADDRESS['privateKey']));
        $this->assertSame(0, $address->getUniverseMagicByte());
        $this->assertSame(self::ADDRESS['publicKey'], $address->getPublicKey()->toHex());
        $this->assertSame(self::ADDRESS['privateKey'], $address->getPrivateKey()->toHex());
    }

    public function testFromHexEncodedPublicKey()
    {
        $kp = radix()->keyService()->generateNew(Secp256k1::class);
        $address = Address::fromPublicKey($kp->getPublicKey()->toHex(), 5, 'hex');
        $this->assertSame($kp->getPublicKey()->toHex(), $address->getPublicKey()->toHex());
    }

    protected function compareToFixture(Address $address, int $universe = self::ADDRESS['universe'])
    {
        $this->assertSame(self::ADDRESS['json'], $address->toJson());
        $this->assertSame(self::ADDRESS['dson'], binaryToBytes((string) $address->toDson()));
        $this->assertSame(self::ADDRESS['str'], $address->toBase58());
        $this->assertSame(self::ADDRESS['hash'], $address->getHash('hex'));
        $this->assertSame(self::ADDRESS['publicKey'], $address->getPublicKey()->toHex());
        if ($address->getPrivateKey() !== null) {
            $this->assertSame(self::ADDRESS['privateKey'], $address->getPrivateKey()->toHex());
        }

        $this->assertSame(self::ADDRESS['uid'], $address->getUID()->toHex());
        $this->assertSame(self::ADDRESS['shard'], $address->getUID()->getShard()->toString(16));
        $this->assertSame($universe, $address->getUniverseMagicByte());
    }
}
