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

namespace Techworker\RadixDLT\Services;

use Techworker\RadixDLT\Crypto\Keys\AdapterInterface;
use Techworker\RadixDLT\Crypto\Keys\CurveResolver;
use Techworker\RadixDLT\Crypto\Keys\KeyPair;
use Techworker\RadixDLT\Crypto\Keys\PrivateKey;
use Techworker\RadixDLT\Crypto\Keys\PublicKey;

class KeyService implements KeyServiceInterface
{
    public function __construct(
        protected array $keyAdapterMapping,
        protected CurveResolver $curveResolver
    ) {
    }

    public function fromPrivateKey(array | string | PrivateKey $privateKey, ?string $enc = 'hex'): KeyPair
    {
        $curve = null;
        // try to get the curve
        if (is_string($privateKey)) {
            $curve = $this->curveResolver->byPrivateKeyLength(
                count(encToBytes($privateKey, $enc))
            );
        } elseif (is_array($privateKey)) {
            $curve = $this->curveResolver->byPrivateKeyLength(count($privateKey));
        } else {
            $curve = $privateKey->getCurve();
        }

        return $this->getAdapter($curve)->fromPrivateKey($privateKey, $enc);
    }

    public function fromPublicKey(array | string | PublicKey $publicKey, ?string $enc = 'hex'): KeyPair
    {
        $curve = null;
        // try to get the curve
        if (is_string($publicKey)) {
            $curve = $this->curveResolver->byPublicKeyLength(
                count(encToBytes($publicKey, $enc))
            );
        } elseif (is_array($publicKey)) {
            $curve = $this->curveResolver->byPublicKeyLength(count($publicKey));
        } else {
            $curve = $publicKey->getCurve();
        }

        return $this->getAdapter($curve)->fromPublicKey($publicKey, $enc);
    }

    public function generateNew(string $curve): KeyPair
    {
        return $this->getAdapter($curve)->generateNew($curve);
    }

    protected function getAdapter(string $curve): AdapterInterface
    {
        $curveClass = $this->curveResolver->byName($curve);
        /** @var AdapterInterface */
        return radix()->get((string) $this->keyAdapterMapping[$curveClass]);
    }
}
