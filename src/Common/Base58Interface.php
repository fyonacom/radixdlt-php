<?php

namespace Techworker\RadixDLT\Common;

interface Base58Interface
{
    public function toBase58();
    public function fromBase58(string $base58);
}
