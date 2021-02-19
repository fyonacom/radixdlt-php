<?php
namespace A;

require_once 'vendor/autoload.php';


use Techworker\RadixDLT\Serialization\Attributes\ArrayProperty;
use Techworker\RadixDLT\Serialization\Attributes\DsonProperty;
use Techworker\RadixDLT\Serialization\Attributes\JsonProperty;

class MyAccount {
    public function __construct(
        #[Radix('name_')]
        #[ArrayProperty('name_')]
        protected string $name,
        #[JsonProperty('universe_', true, true)]
        #[ArrayProperty('universe_')]
        protected string $read,
        #[JsonProperty('transactions_', true, true)]
        #[ArrayProperty('transactions_')]
        protected array $transactions,
    )
    {
    }
}

class MyTransaction {
    public function __construct(
        #[JsonProperty('id_')]
        #[ArrayProperty('id_')]
        protected string $id,
        #[JsonProperty('title_')]
        #[ArrayProperty('title_')]
        protected string $title
    )
    {
    }
}

$transaction = new MyTransaction('1', 'Test 1');
$transaction2 = new MyTransaction('2', 'Test 2');

$t = new MyAccount('benjamin', '1', [$transaction, $transaction2]);
print_r(toArray($t));
