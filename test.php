<?php
namespace A;

use BN\BN;
use CBOR\UnsignedIntegerObject;
use Techworker\RadixDLT\Radix;
use Techworker\RadixDLT\Serialization\ComplexSerializer;

require_once 'vendor/autoload.php';

Radix::bootstrap();

$data = [
    'epoch' => 1,
    'timestamp' => 1612357246596,
    'view' => 1000,
    'serializer' => 'radix.particles.system_particle'
];

$dson = 'bf6565706f6368016a73657269616c697a6572781f72616469782e7061727469636c65732e73797374656d5f7061727469636c656974696d657374616d701b0000017767fb1e8464766965771903e8ff';

/** @var ComplexSerializer $serializer */
$serializer = radix()->get(ComplexSerializer::class);
print_r($serializer->fromJson($data));

exit;

$number = 10;

echo bytesToFormattedHex(binaryToBytes((string)UnsignedIntegerObject::create(10)));
echo "\n";
echo bytesToFormattedHex(binaryToBytes((string)UnsignedIntegerObject::create(128)));
echo "\n";
echo bytesToFormattedHex(binaryToBytes((string)UnsignedIntegerObject::create(256)));
echo "\n";
echo bytesToFormattedHex(binaryToBytes((string)UnsignedIntegerObject::create(500)));

exit;
$v = hexToString((string)(new BN((string)$data))->toString(16, 8));
if ($v[0] === '-') {
    $unsigned = true;
}

return UnsignedIntegerObject::createObjectForValue(27, $v);



$jsonConfig = json_decode(file_get_contents(__DIR__ . '/universe.json'), true);
$config = \radix()->get(ComplexSerializer::class)->fromJson($jsonConfig);
$backJson = $config->toJson();
echo \radix()->get(ComplexSerializer::class)->toDson($config);

print_r(array_diff_assoc_recursive($jsonConfig, $backJson));
function array_diff_assoc_recursive($array1, $array2) {
    $difference=array();
    foreach($array1 as $key => $value) {
        if( is_array($value) ) {
            if( !isset($array2[$key]) || !is_array($array2[$key]) ) {
                $difference[$key] = $value;
            } else {
                $new_diff = array_diff_assoc_recursive($value, $array2[$key]);
                if( !empty($new_diff) )
                    $difference[$key] = $new_diff;
            }
        } else if( !array_key_exists($key,$array2) || $array2[$key] !== $value ) {
            $difference[$key] = $value;
        }
    }
    return $difference;
}
