<?php

namespace Techworker\RadixDLT\Serialization;

interface ToJsonInterface
{
    public function toJson() : array|string;
}
