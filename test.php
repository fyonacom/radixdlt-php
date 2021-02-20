<?php
namespace A;

use BN\BN;
use Techworker\RadixDLT\Types\Primitives\UInt256;
use Techworker\RadixDLT\Types\Universe\UniverseConfig;

require_once 'vendor/autoload.php';

$a = UInt256::fromJson(':u20:1000000000000000000');
print_r($a->toBytes());
