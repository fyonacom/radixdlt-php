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

namespace Techworker\RadixDLT\Types;

use Techworker\RadixDLT\Serialization\Interfaces\FromJsonInterface;
use Techworker\RadixDLT\Types\Particles\ParticleGroup;
use Techworker\RadixDLT\Types\Primitives\String_;

class Atom implements FromJsonInterface
{
    public function __construct(
        protected array $particleGroups,
        protected array $signatures,
        protected String_ $message,
    ) {
    }

    public static function fromJson(array | string $json): static
    {
        $data = [
            'particleGroups' => [],
            'signatures' => [],
        ];
        $data['message'] = String_::fromJson($json['message']);
        /** @var array $json['particleGroups'] */
        foreach ($json['particleGroups'] as $group) {
            $data['particleGroups'][] = ParticleGroup::fromJson($group);
        }
        foreach ($json['signatures'] as $group) {
            $data['particleGroups'][] = ParticleGroup::fromJson($group);
        }
    }
}
