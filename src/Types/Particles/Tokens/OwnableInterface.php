<?php

namespace Techworker\RadixDLT\Types\Particles\Tokens;

use Techworker\RadixDLT\Types\Core\Address;

interface OwnableInterface {
    public function getOwner() : Address;
}
