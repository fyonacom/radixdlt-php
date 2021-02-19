<?php

namespace Techworker\RadixDLT\Tests;

use Techworker\RadixDLT\Radix;

class TestCase extends \PHPUnit\Framework\TestCase
{
    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        Radix::setup();
    }
}
