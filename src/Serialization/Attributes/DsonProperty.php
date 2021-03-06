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

#[Attribute(Attribute::TARGET_PROPERTY)]
class DsonProperty extends AbstractAttribute
{
    protected ?string $type = null;

    protected bool $nullable = false;

    protected ?string $name = null;

    protected mixed $defaultValue = '__NO_VALUE_RADIXDLT_PHP__';

    public function __construct(
        protected ?string $key = null,
        protected ?string $arraySubType = null,
        protected bool $int64 = false,
    ) {
    }


    public function getKey(): ?string
    {
        return $this->key;
    }


    public function getArraySubType(): ?string
    {
        return $this->arraySubType;
    }


    public function isInt64(): bool
    {
        return $this->int64;
    }


    public function getType(): ?string
    {
        return $this->type;
    }


    public function setType(?string $type): void
    {
        $this->type = $type;
    }


    public function isNullable(): bool
    {
        return $this->nullable;
    }


    public function setNullable(bool $nullable): void
    {
        $this->nullable = $nullable;
    }


    public function getName(): ?string
    {
        return $this->name;
    }


    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return mixed|string
     */
    public function getDefaultValue(): mixed
    {
        return $this->defaultValue;
    }

    /**
     * @param mixed|string $defaultValue
     */
    public function setDefaultValue(mixed $defaultValue): void
    {
        $this->defaultValue = $defaultValue;
    }
}
