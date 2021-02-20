<?php
namespace A;

use BN\BN;
use CBOR\ByteStringObject;
use CBOR\Decoder;
use CBOR\InfiniteMapObject;
use CBOR\MapObject;
use CBOR\OtherObject\OtherObjectManager;
use CBOR\StringStream;
use CBOR\Tag\PositiveBigIntegerTag;
use CBOR\Tag\TagObjectManager;
use CBOR\TextStringObject;
use CBOR\UnsignedIntegerObject;
use Techworker\RadixDLT\Types\Particles\SystemParticle;
use Techworker\RadixDLT\Types\Primitives\UInt256;
use Techworker\RadixDLT\Types\Universe\UniverseConfig;

require_once 'vendor/autoload.php';

$mo = new InfiniteMapObject();
$mo->append(new TextStringObject('epoch'), UnsignedIntegerObject::create(1));
$mo->append(new TextStringObject('serializer'), new TextStringObject('radix.particles.system_particle'));
//$v = PositiveBigIntegerTag::create(new ByteStringObject();
$mo->append(new TextStringObject('timestamp'), UnsignedIntegerObject::createObjectForValue(27, hexToString((new BN('1612357246596'))->toString(16, 8))));
$mo->append(new TextStringObject('view'), UnsignedIntegerObject::create(1000));
//print_r($mo);
//exit;
print_r(bytesToHex(binaryToBytes((string)$mo)));
exit;
//print_r(binaryToBytes((string)$mo));

$stream = new StringStream(hexToString('bf6565706f6368016a73657269616c697a6572781f72616469782e7061727469636c65732e73797374656d5f7061727469636c656974696d657374616d701b0000017767fb1e8464766965771903e8ff'));
$decoder = new Decoder(new TagObjectManager(), new OtherObjectManager());
$decoded = $decoder->decode($stream);
print_r($decoded);


//bf6565706f6368016a73657269616c697a6572781f72616469782e7061727469636c65732e73797374656d5f7061727469636c656974696d657374616d701b0000017767fb1e8464766965771903e8ff
//bf6565706f6368016a73657269616c697a6572781f72616469782e7061727469636c65732e73797374656d5f7061727469636c656974696d657374616d701b    017767fb1e8464766965771903e8ff
