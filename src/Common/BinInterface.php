<?php

namespace Techworker\RadixDLT\Common;

interface BinInterface
{
    public function toBin();
    public function fromBin(string $bin);
}
