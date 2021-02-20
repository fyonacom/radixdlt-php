<?php

declare(strict_types=1);

/*
 * This file is part of the RADIXDLT PHP package.
 *
 * (c) Copyright >=2020 Benjamin Ansbach & fyona.com <ben@fyona.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Techworker\RadixDLT\Crypto\Keys\Adapters;

use DomainException;
use RuntimeException;
use Techworker\RadixDLT\Crypto\Keys\AdapterInterface;
use Techworker\RadixDLT\Crypto\Keys\CurveResolver;
use Techworker\RadixDLT\Crypto\Keys\KeyPair;
use Techworker\RadixDLT\Crypto\Keys\PrivateKey;
use Techworker\RadixDLT\Crypto\Keys\PublicKey;

/**
 * Class OpenSSL
 *
 * A adapter to handle keys using openssl which is much faster than custom
 * made libraries.
 */
class OpenSSL implements AdapterInterface
{
    /**
     * OpenSSL constructor.
     *
     * @param array $openSSLConfig
     * @param CurveResolver $curveResolver
     */
    public function __construct(
        protected array $openSSLConfig,
        protected CurveResolver $curveResolver
    ) {
    }

    public function generateNew(string $curve): KeyPair
    {
        // try to fetch the curve by the given name
        $curve = $this->curveResolver->byName($curve);

        // check if openssl supports the given curve
        if (! isset($this->openSSLConfig[$curve])) {
            throw new DomainException(
                'OpenSSL Error. Unable to generate a keypair fur curve: ' . $curve
            );
        }

        $response = openssl_pkey_new((array) $this->openSSLConfig[$curve]);
        openssl_pkey_export($response, $openSslPrivateKey);
        return $this->fromPrivateKey($openSslPrivateKey);
    }

    /**
     * @param string|int[]|PublicKey $publicKey
     */
    public function fromPublicKey(string | array | PublicKey $publicKey, ?string $enc = 'hex'): KeyPair
    {
        if (is_string($publicKey)) {
            $bytes = encToBytes($publicKey, $enc);
        } elseif (is_array($publicKey)) {
            $bytes = $publicKey;
        } else {
            $bytes = $publicKey->toBytes();
        }

        /** @var KeyPair $libKeyPair */
        return new KeyPair(PublicKey::fromBytes($bytes));
    }

    public function fromPrivateKey(string | array | PrivateKey $privateKey, ?string $enc = 'hex'): KeyPair
    {
        $pem = null;
        $bytes = [];
        if (is_string($privateKey)) {
            if (str_starts_with($privateKey, '-----')) {
                $pem = $privateKey;
            } else {
                $bytes = encToBytes($privateKey, $enc);
            }
        } elseif (is_array($privateKey)) {
            $bytes = $privateKey;
        } else {
            $pem = $privateKey->toPEM();
        }

        // no pem given so we will create our own, thats what openssl can
        // understand
        if ($pem === null) {
            $pem = PrivateKey::fromBytes($bytes)->toPem();
        }

        // import and fetch key data
        $openSslPrivateKey = openssl_pkey_get_private($pem);
        $keyDetails = openssl_pkey_get_details($openSslPrivateKey);
        if ($keyDetails === false) {
            throw new RuntimeException(
                'OpenSSL Error. ' . openssl_error_string()
            );
        }

        // compressed..
        /** @var array $ecKey */
        $ecKey = $keyDetails['ec'];
        $pubX = binaryToBytes((string) $ecKey['x']);
        $pubY = binaryToBytes((string) $ecKey['y']);
        if ($pubY[count($pubY) - 1] % 2 === 0) {
            array_unshift($pubX, 0x02);
        } else {
            array_unshift($pubX, 0x03);
        }

        $privateKey = PrivateKey::fromBinary((string) $ecKey['d']);
        $publicKey = PublicKey::fromBytes($pubX);
        return new KeyPair($publicKey, $privateKey);
    }
}
