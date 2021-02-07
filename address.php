<?php

require __DIR__ . '/vendor/autoload.php';

echo \Techworker\RadixDLT\Serialization\JsonPrefix::getPrefix(\Techworker\RadixDLT\Primitives\RadixAddress::class);
