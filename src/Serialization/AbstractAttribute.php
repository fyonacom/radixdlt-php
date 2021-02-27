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

namespace Techworker\RadixDLT\Serialization;

use ReflectionClass;
use Techworker\RadixDLT\Serialization\Attributes\DsonProperty;
use Techworker\RadixDLT\Serialization\Attributes\JsonProperty;
use Throwable;

/**
 * Class AbstractAttribute
 *
 * A simple abstract attribute to provide useful methods to certain
 * attributes.
 */
abstract class AbstractAttribute
{
    /**
     * Will get the current attribute assigned to the given class.
     *
     * @param string|object $classOrInstance
     * @return static|null
     */
    public static function getClassAttribute(string | object $classOrInstance): ?self
    {
        /** @var AbstractAttribute[] $values */
        static $values = [];

        /** @var class-string $class */
        $class = $classOrInstance;
        if (! is_string($classOrInstance)) {
            $class = $classOrInstance::class;
        }

        if (! isset($values[$class])) {
            $values[$class] = [];
        }

        if (! isset($values[$class][static::class])) {
            try {
                $reflection = new ReflectionClass($class);
            } catch (Throwable) {
                return null;
            }

            $attributes = $reflection->getAttributes(static::class);
            foreach ($attributes as $attribute) {
                $values[$class][static::class] = $attribute->newInstance();
            }
        }

        return $values[$class][static::class] ?? null;
    }

    /**
     * Gets all properties with the current attribute in the given class.
     *
     * @param string|object $classOrInstance
     */
    public static function getProperties(string | object $classOrInstance): array
    {
        /** @var mixed[] $values */
        static $values = [];

        /** @var class-string $class */
        $class = $classOrInstance;
        if (! is_string($classOrInstance)) {
            $class = $classOrInstance::class;
        }
        if (! isset($values[$class])) {
            $values[$class] = [];
        }

        if (! isset($values[$class][static::class])) {
            $values[$class][static::class] = [];
            try {
                $reflection = new ReflectionClass($class);
            } catch (Throwable) {
                // TODO: silent failure ok? I think not
                return [];
            }

            $properties = $reflection->getProperties();
            foreach ($properties as $property) {
                $attributes = $property->getAttributes(static::class);
                foreach ($attributes as $attribute) {
                    $attr = $attribute->newInstance();

                    // TODO: that should be put somewhere else, the abstract class
                    // doesn't know about the existence of JsonProperty and
                    // DsonProperty
                    if ($attr instanceof JsonProperty || $attr instanceof DsonProperty) {
                        $attr->setNullable($property->getType()->allowsNull());
                        $attr->setName($property->getName());
                        $attr->setType($property->getType()->getName());
                        $attr->setDefaultValue($property->getDefaultValue());
                    }
                    $values[$class][static::class][$property->getName()] = $attr;
                }
            }
        }

        return $values[$class][static::class];
    }
}
