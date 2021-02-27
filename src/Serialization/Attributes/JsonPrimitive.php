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
use Techworker\RadixDLT\Serialization\AttributeHasEncodingInterface;

#[Attribute(Attribute::TARGET_CLASS)]
class JsonPrimitive extends AbstractAttribute implements AttributeHasEncodingInterface
{
    public function __construct(
        protected string $prefix,
        protected ?string $property = null,
        protected ?string $encoding = null
    ) {
    }


    public function getPrefix(): string
    {
        return $this->prefix;
    }


    public function getProperty(): ?string
    {
        return $this->property;
    }


    public function getEncoding(): ?string
    {
        return $this->encoding;
    }
}
