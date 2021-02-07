<?php

namespace Techworker\RadixDLT\Tests;

use PHPUnit\Framework\TestCase;
use Techworker\RadixDLT\Crypto\Keys\Curves\Secp256k1;
use Techworker\RadixDLT\Crypto\Keys\PrivateKey;
use Techworker\RadixDLT\Crypto\Keys\PublicKey;
use Techworker\RadixDLT\Primitives\RadixAddress;
use function Techworker\RadixDLT\hexToBytes;
use function Techworker\RadixDLT\stringToHex;

class RadixAddressTest extends TestCase
{
    public function fixtureProvider() {
        //return array_slice(json_decode(file_get_contents(__DIR__ . '/fixtures/RadixAddress.json'), true), 0, 1);
        return json_decode(file_get_contents(__DIR__ . '/fixtures/RadixAddress.json'), true);
    }

    /**
     * @dataProvider fixtureProvider
     * @param string $publicKey
     * @param string $privateKey
     * @param string $base58
     * @param array $bytes
     * @param string $hash
     * @param string $shard
     * @param string $uid
     * @param string $json
     * @param string $dson
     * @param int $universe
     * @throws \Exception
     */
    public function testFromBase58(string $publicKey,
                                    string $privateKey,
                                    string $base58,
                                    array $bytes,
                                    string $hash,
                                    string $shard,
                                    string $uid,
                                    string $json,
                                    string $dson,
                                    int $universe) {
        $radixAddress = RadixAddress::from($base58);
        $this->assertRadixAddress(
            $radixAddress, $publicKey, null, $base58, $bytes,
            $hash, $shard, $uid, $json, $dson, $universe
        );
    }

    /**
     * @dataProvider fixtureProvider
     * @param string $publicKey
     * @param string $privateKey
     * @param string $address
     * @param array $addressBytes
     * @param string $hash
     * @param string $shard
     * @param string $uid
     * @param string $json
     * @param string $dson
     * @param int $magicByte
     */
    public function testFromPublicKey(string $publicKey,
                                   string $privateKey,
                                   string $base58,
                                   array $bytes,
                                   string $hash,
                                   string $shard,
                                   string $uid,
                                   string $json,
                                   string $dson,
                                   int $universe) {

        $publicKey = new PublicKey(Secp256k1::class, hexToBytes($publicKey));

        // create from public key array and public key hex
        $addresses = [
            RadixAddress::fromPublicKey($publicKey->to('hex'), 'hex', $universe),
            RadixAddress::fromPublicKey($publicKey->to('bytes'), null, $universe),
            RadixAddress::fromPublicKey($publicKey, null, $universe)
        ];

        // loop and check
        foreach($addresses as $radixAddress) {
            $this->assertRadixAddress(
                $radixAddress, $publicKey, null, $base58, $bytes,
                $hash, $shard, $uid, $json, $dson, $universe
            );
        }
    }

    /**
     * @dataProvider fixtureProvider
     * @param string $publicKey
     * @param string $privateKey
     * @param string $base58
     * @param array $bytes
     * @param string $hash
     * @param string $shard
     * @param string $uid
     * @param string $json
     * @param string $dson
     * @param int $universe
     */
    public function testFromPrivate(string $publicKey,
                                   string $privateKey,
                                   string $base58,
                                   array $bytes,
                                   string $hash,
                                   string $shard,
                                   string $uid,
                                   string $json,
                                   string $dson,
                                   int $universe) {

        $privateKey = new PrivateKey(Secp256k1::class, hexToBytes($privateKey));

        // create from public key array and public key hex
        $addresses = [
            RadixAddress::fromPrivateKey($privateKey->to('hex'), 'hex', $universe),
            RadixAddress::fromPrivateKey($privateKey->to('bytes'), null, $universe),
            RadixAddress::fromPrivateKey($privateKey, null, $universe)
        ];

        foreach($addresses as $radixAddress) {
            $this->assertRadixAddress(
                $radixAddress, $publicKey, $privateKey, $base58, $bytes,
                $hash, $shard, $uid, $json, $dson, $universe
            );
        }
    }

    private function assertRadixAddress(
        RadixAddress $radixAddress,
        string $publicKey,
                                        ?string $privateKey,
                                        string $base58,
                                        array $bytes,
                                        string $hash,
                                        string $shard,
                                        string $uid,
                                        string $json,
                                        string $dson,
                                        int $universe)
    {
        $this->assertEquals($publicKey, $radixAddress->getPublicKey()->to('hex'));
        $this->assertEquals($privateKey, $radixAddress->getPrivateKey()?->to('hex'));

        $this->assertEquals($base58, $radixAddress->to('base58'));
        $this->assertEquals($base58, (string)$radixAddress);

        $this->assertEquals($bytes, $radixAddress->to('bytes'));

        $this->assertEquals($hash, $radixAddress->getHash('hex'));
        $this->assertEquals($json, $radixAddress->to('json'));
        $this->assertEquals($universe, $radixAddress->getUniverse());

        $this->assertEquals($shard, $radixAddress->getShard('hex'));
        $this->assertEquals($uid, (string)$radixAddress->getUID());
        $this->assertEquals($dson, stringToHex($radixAddress->cbor()));
    }
}
