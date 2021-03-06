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

namespace Techworker\RadixDLT\Serialization\Attributes;

use Attribute;
use Techworker\RadixDLT\Serialization\AbstractAttribute;

#[Attribute(Attribute::TARGET_CLASS)]
class Serializer extends AbstractAttribute
{
    public function __construct(
        protected string $name
    ) {
    }


    public function getName(): string
    {
        return $this->name;
    }
}
