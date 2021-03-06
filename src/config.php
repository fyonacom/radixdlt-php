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

namespace Techworker\RadixDLT;

use Techworker\RadixDLT\Crypto\Keys\Adapters\OpenSSL;
use Techworker\RadixDLT\Crypto\Keys\Adapters\SimplitoElliptic;
use Techworker\RadixDLT\Crypto\Keys\Curves\Secp256k1;

return [
    'connections' => [
        'localhost' => [
            'ws' => 'ws://172.17.0.1:8080/rpc',
            'rpc' => 'http://172.17.0.1:8080/rpc',
            'api' => 'http://172.17.0.1:8080/api'
        ]
    ],
    'crypto' => [
        'keys' => [
            'supported' => [
                Secp256k1::class,
            ],
            'mapping' => [
                Secp256k1::class => OpenSSL::class,
            ],
            OpenSSL::class => [
                Secp256k1::class => [
                    'digest_alg' => 'sha256',
                    'private_key_bits' => 2048,
                    'private_key_type' => OPENSSL_KEYTYPE_EC,
                    'curve_name' => 'secp256k1',
                ],
            ],
            SimplitoElliptic::class => [
            ],
        ],
    ],
];
