<?php

namespace Techworker\RadixDLT\Tests;

use PHPUnit\Framework\TestCase;
use Techworker\RadixDLT\Crypto\Keys\PrivateKey;
use Techworker\RadixDLT\Crypto\Keys\PublicKey;
use Techworker\RadixDLT\Radix;
use Techworker\RadixDLT\Types\Core\Address;

class AddressTestOld extends TestCase
{
    public function fixtureProvider()
    {
        //return array_slice(json_decode(file_get_contents(__DIR__ . '/fixtures/RadixAddress.json'), true), 0, 1);
        return json_decode(file_get_contents(__DIR__ . '/fixtures/RadixAddress.json'), true);
    }

    /**
     * @dataProvider fixtureProvider
     * @throws \ReflectionException
     */
    public function testFromBase58(
        string $keyType,
        string $publicKey,
        string $privateKey,
        string $base58,
        array $bytes,
        string $hash,
        string $shard,
        string $uid,
        string $json,
        string $dson,
        int $universe
    ) {
        Radix::setup();
        $radixAddress = Address::fromBase58($base58);
        $this->assertRadixAddress(
            $radixAddress,
            $keyType,
            $publicKey,
            null,
            $base58,
            $bytes,
            $hash,
            $shard,
            $uid,
            $json,
            $dson,
            $universe
        );
    }

    /**
     * @dataProvider fixtureProvider
     */
    public function testFromPublicKey(
        string $keyType,
        string $publicKey,
        string $privateKey,
        string $base58,
        array $bytes,
        string $hash,
        string $shard,
        string $uid,
        string $json,
        string $dson,
        int $universe
    ) {
        $publicKey = PublicKey::fromHex($publicKey);

        // create from public key array and public key hex
        $addresses = [
            Address::fromPublicKey($publicKey->toHex(), 'hex', $universe),
            Address::fromPublicKey($publicKey->toBytes(), null, $universe),
            Address::fromPublicKey($publicKey, null, $universe),
        ];

        // loop and check
        foreach ($addresses as $radixAddress) {
            $this->assertRadixAddress(
                $radixAddress,
                $keyType,
                $publicKey,
                null,
                $base58,
                $bytes,
                $hash,
                $shard,
                $uid,
                $json,
                $dson,
                $universe
            );
        }
    }

    /**
     * @dataProvider fixtureProvider
     */
    public function testFromPrivate(
        string $keyType,
        string $publicKey,
        string $privateKey,
        string $base58,
        array $bytes,
        string $hash,
        string $shard,
        string $uid,
        string $json,
        string $dson,
        int $universe
    ) {
        $privateKey = PrivateKey::fromHex($privateKey);

        // create from public key array and public key hex
        $addresses = [
            Address::fromPrivateKey($privateKey->toHex(), 'hex', $universe),
            Address::fromPrivateKey($privateKey->toBytes(), null, $universe),
            Address::fromPrivateKey($privateKey, null, $universe),
        ];

        foreach ($addresses as $radixAddress) {
            $this->assertRadixAddress(
                $radixAddress,
                $keyType,
                $publicKey,
                $privateKey,
                $base58,
                $bytes,
                $hash,
                $shard,
                $uid,
                $json,
                $dson,
                $universe
            );
        }
    }

    /**
     * @dataProvider fixtureProvider
     * @param int[] $bytes
     */
    public function testFrom(
        string $keyType,
        string $publicKey,
        string $privateKey,
        string $base58,
        array $bytes,
        string $hash,
        string $shard,
        string $uid,
        string $json,
        string $dson,
        int $universe
    ) {

        // create from public key array and public key hex
        $addresses = [
            Address::fromBase58($base58),
            Address::fromHex(bytesToHex($bytes)),
            Address::fromBinary(bytesToBinary($bytes)),
            Address::fromBytes($bytes),
        ];

        foreach ($addresses as $radixAddress) {
            $this->assertRadixAddress(
                $radixAddress,
                $keyType,
                $publicKey,
                null,
                $base58,
                $bytes,
                $hash,
                $shard,
                $uid,
                $json,
                $dson,
                $universe
            );
        }
    }

    private function assertRadixAddress(
        Address $radixAddress,
        string $keyType,
        string $publicKey,
        ?string $privateKey,
        string $base58,
        array $bytes,
        string $hash,
        string $shard,
        string $uid,
        string $json,
        string $dson,
        int $universe
    ) {
        $this->assertSame($publicKey, $radixAddress->getPublicKey()->to('hex'));
        $this->assertSame($privateKey, $radixAddress->getPrivateKey()?->to('hex'));

        $this->assertSame($base58, $radixAddress->toBase58());
        $this->assertSame($base58, (string) $radixAddress);

        $this->assertSame($bytes, $radixAddress->toBytes());

        $this->assertSame($hash, $radixAddress->getHash('hex'));
        $this->assertSame($json, $radixAddress->toJson());
        $this->assertSame($universe, $radixAddress->getUniverseMagicByte());

        $this->assertSame($shard, $radixAddress->getUID()->getShard('hex'));
        $this->assertSame($uid, (string) $radixAddress->getUID());
        //$this->assertEquals($dson, stringToHex($radixAddress->cbor()));
    }
}
