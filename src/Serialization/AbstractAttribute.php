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

use Attribute;
use ReflectionClass;
use ReflectionException;
use Throwable;

abstract class AbstractAttribute
{
    /**
     * Gets an attribute value
     *
     * @param $classOrInstance
     * @return string|null
     * @throws ReflectionException
     */
    public static function getParam(string $name, $classOrInstance) : mixed {
        static $values = [];

        $class = $classOrInstance;
        if(!is_string($classOrInstance)) {
            $class = $classOrInstance::class;
        }

        $acc = $class . '::' . $name;
        if(!isset($values[$acc])) {
            $reflection = new ReflectionClass($classOrInstance);

            $attributes = $reflection->getAttributes(static::class);
            foreach ($attributes as $attribute) {
                $values[$acc] = $attribute->getArguments()[$name];
                break;
            }
        }

        return $values[$acc];
    }
}
