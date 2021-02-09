<?php

namespace Techworker\RadixDLT\Serialization;

interface FromJsonInterface
{
    public static function fromJson(string|array $json) : self;
}
