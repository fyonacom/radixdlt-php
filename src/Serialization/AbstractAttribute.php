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
use Throwable;

abstract class AbstractAttribute
{
    /**
     * Gets an attribute value.
     *
     * @param string $name
     * @param $classOrInstance
     * @param mixed $default
     * @return mixed
     */
    public static function getParam(string $name, string|object $classOrInstance, mixed $default = null) : mixed {
        /** @var mixed[] $values */
        static $values = [];

        /** @var class-string $class */
        $class = $classOrInstance;
        if(!is_string($classOrInstance)) {
            $class = $classOrInstance::class;
        }

        $acc = $class . '::' . $name;
        if(!isset($values[$acc])) {
            try {
                $reflection = new ReflectionClass($class);
            }
            catch(Throwable) {
                return null;
            }

            $attributes = $reflection->getAttributes(static::class);
            foreach ($attributes as $attribute) {
                if(!isset($attribute->getArguments()[$name])) {
                    // argument not given in attribute usage, so we will try
                    // and fetch the property from the attribute instance
                    $instance = $attribute->newInstance();
                    /** @var mixed */
                    $values[$acc] = $instance->{$name};
                } else {
                    $values[$acc] = $attribute->getArguments()[$name];
                }
                break;
            }
        }

        return $values[$acc];
    }
}
