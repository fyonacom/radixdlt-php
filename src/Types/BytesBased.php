<?php

declare(strict_types=1);

/*
 * This file is part of the RADIXDLT PHP package.
 *
 * (c) Copyright >=2020 Benjamin Ansbach & fyona.com <ben@fyona.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Techworker\RadixDLT\Types;

use CBOR\AbstractCBORObject;
use Exception;
use Techworker\RadixDLT\Serialization\Attributes\CBOR;
use Techworker\RadixDLT\Serialization\Attributes\Encoding;
use Techworker\RadixDLT\Serialization\Attributes\Json;
use Techworker\RadixDLT\Serialization\FromJsonInterface;
use Techworker\RadixDLT\Serialization\ToJsonInterface;
use function Techworker\RadixDLT\base64ToBytes;
use function Techworker\RadixDLT\binaryToBytes;
use function Techworker\RadixDLT\base58ToBytes;
use function Techworker\RadixDLT\bytesToBase58;
use function Techworker\RadixDLT\bytesToBase64;
use function Techworker\RadixDLT\bytesToEnc;
use function Techworker\RadixDLT\bytesToBinary;
use function Techworker\RadixDLT\bytesToHex;
use function Techworker\RadixDLT\hexToBytes;
use function Techworker\RadixDLT\stringToHex;

/**
 * Class AbstractPrimitive
 *
 * Basic primitive with common utility functionality.
 * @psalm-consistent-constructor
 */
abstract class BytesBased
    implements ToJsonInterface, FromJsonInterface
{
    /**
     * AbstractPrimitive constructor.
     * @param int[] $bytes
     */
    protected function __construct(protected array $bytes)
    {
    }

    /**
     * Gets the CBOR encoded object.
     *
     * @return AbstractCBORObject
     */
    public function toCbor(): AbstractCBORObject
    {
        $bytes = $this->toBytes();
        array_unshift($bytes, static::getCborPrefix());
        /** @var class-string $type */
        $type = static::getCborTarget();

        $result = new $type(bytesToBinary($bytes));
        /** @var AbstractCBORObject $result */
        return $result;
    }

    /**
     * Gets the bytes of this instance.
     *
     * @return int[]
     */
    public function toBytes() : array {
        if(!self::isSupportedEncoding('bytes')) {
            throw new \InvalidArgumentException('toBytes not supported in ' . static::class);
        }
        return $this->bytes;
    }

    /**
     * Gets the json representation.
     *
     * @return string|array
     * @throws Exception
     */
    public function toJson() : string|array {
        if(!self::isSupportedEncoding('json')) {
            throw new \InvalidArgumentException('toJson not supported in ' . static::class);
        }

        $prefix = static::getJsonPrefix();
        $encoding = static::getJsonEncoding();
        if($prefix === null || $encoding === null) {
            // TODO: More specific exception, thats a developer error
            throw new Exception(
                'Please set the json encoding/prefix configuration for class: ' . static::class
            );
        }

        $encoded = $this->to($encoding);
        if(is_array($encoded)) {
            throw new \Exception('...');
        }

        return $prefix . $encoded;
    }

    /**
     * Gets the base58 representation.
     *
     * @return string
     */
    public function toBase58() : string {
        if(!self::isSupportedEncoding('base58')) {
            throw new \InvalidArgumentException('toBase58 not supported in ' . static::class);
        }
        return bytesToBase58($this->bytes);
    }

    /**
     * Gets the hex representation.
     *
     * @return string
     */
    public function toHex() : string {
        if(!self::isSupportedEncoding('hex')) {
            throw new \InvalidArgumentException('toHex not supported in ' . static::class);
        }
        return bytesToHex($this->bytes);
    }

    /**
     * Gets the binary string representation.
     *
     * @return string
     */
    public function toBinary() : string {
        if(!self::isSupportedEncoding('bin')) {
            throw new \InvalidArgumentException('toBinary not supported in ' . static::class);
        }
        return bytesToBinary($this->bytes);
    }

    /**
     * Gets the binary string representation.
     *
     * @return string
     */
    public function toBase64() : string {
        if(!self::isSupportedEncoding('base64')) {
            throw new \InvalidArgumentException('toBase64 not supported in ' . static::class);
        }
        return bytesToBase64($this->bytes);
    }

    /**
     * Gets the representation based on the given encoding.
     *
     * @param string|null $enc
     * @return array|string
     * @throws Exception
     */
    public function to(string $enc = null): array|string
    {
        return match ($enc) {
            'bytes' => $this->toBytes(),
            'bin' => $this->toBinary(),
            'json' => $this->toJson(),
            // 'cbor' => $this->toCbor(),
            'hex' => $this->toHex(),
            'base58' => $this->toBase58(),
            'base64' => $this->toBase64(),
            default => bytesToEnc($this->bytes, $enc ?? static::getEncoding())
        };
    }

    /**
     * Creates a new primitive instance from the given hex string.
     *
     * @param string $hex
     * @return static
     */
    public static function fromHex(string $hex) : static {
        if(!self::isSupportedEncoding('hex')) {
            throw new \InvalidArgumentException('fromHex not supported in ' . static::class);
        }
        return new static(hexToBytes($hex));
    }

    /**
     * Creates a new primitive instance from the given binary string.
     *
     * @param string $binary
     * @return static
     */
    public static function fromBinary(string $binary) : static {
        if(!self::isSupportedEncoding('bin')) {
            throw new \InvalidArgumentException('fromBin not supported in ' . static::class);
        }
        return new static(binaryToBytes($binary));
    }

    /**
     * Creates a new primitive instance from the given base58 string.
     *
     * @param string $base58
     * @return static
     */
    public static function fromBase58(string $base58) : static {
        if(!self::isSupportedEncoding('base58')) {
            throw new \InvalidArgumentException('fromBase58 not supported in ' . static::class);
        }
        return new static(base58ToBytes($base58));
    }

    /**
     * Creates a new primitive instance from the given base58 string.
     *
     * @param string $base64
     * @return static
     */
    public static function fromBase64(string $base64) : static {
        if(!self::isSupportedEncoding('base64')) {
            throw new \InvalidArgumentException('fromBase64 not supported in ' . static::class);
        }
        return new static(base64ToBytes($base64));
    }

    /**
     * Creates a new primitive instance from the given json string.
     *
     * @param string|array $json
     * @return static
     * @throws Exception
     */
    public static function fromJson(string|array $json) : static {
        if(!self::isSupportedEncoding('json')) {
            throw new \InvalidArgumentException('fromJson not supported in ' . static::class);
        }

        if(is_array($json)) {
            throw new \InvalidArgumentException('Err');
        }

        $prefix = static::getJsonPrefix();
        $encoding = static::getJsonEncoding();
        if($prefix === null || $encoding === null) {
            // TODO: More specific exception, thats a developer error
            throw new Exception(
                'Please set the json encoding/prefix configuration for class: ' . static::class
            );
        }

        return static::from(substr($json, strlen($prefix)), $encoding);
    }

    /**
     * Creates a new instance of the implementing class.
     *
     * @param int[] $bytes
     * @return static
     */
    public static function fromBytes(array $bytes) : static {
        return new static($bytes);
    }

    /**
     * Initializes a new instance.
     *
     * @param string|int[] $data
     * @param string|null $enc
     * @return static
     */
    public static function from(string|array $data, string $enc = null) : BytesBased {
        if(is_string($data)) {
            return match ($enc) {
                'bin' => static::fromBinary($data),
                'json' => static::fromJson($data),
                // TODO: I'm not able to check it right now, should do it..
                //'cbor' => static::fromCbor($data),
                'hex' => static::fromHex($data),
                'base58' => static::fromBase58($data),
                'base64' => static::fromBase64($data),
                default => static::getEncoding() !== $enc ? static::from($data, static::getEncoding()) : throw new \Exception('Recursion detected.')
            };
        }

        return static::fromBytes($data);
    }

    /**
     * Gets the string representation of the primitive using the default encoding.
     * @return string
     */
    public function __toString(): string
    {
        /** @var string $encoding */
        $encoding = $this->to(static::getEncoding());
        return $encoding;
    }

    /**
     * Helper to retrieve the encoding for the primitive.
     *
     * @return string|null
     */
    protected static function getEncoding() : ?string {
        /** @var string|null $encoding */
        $encoding = Encoding::getParam('encoding', static::class);
        return $encoding;
    }

    /**
     * Helper to retrieve the encoding for parts of a primitive.
     *
     * @param string $encoding
     * @return bool
     */
    protected static function isSupportedEncoding(string $encoding) : bool {
        /** @var string[] $supported */
        $supported = Encoding::getParam('supported', static::class, []);

        /** @var string[] $notSupported */
        $notSupported = Encoding::getParam('notSupported', static::class, []);

        $validEncodings = array_diff($supported, $notSupported);
        return in_array($encoding, $validEncodings, true);
    }

    /**
     * Helper to retrieve the json prefix.
     *
     * @return string|null
     */
    protected static function getJsonPrefix() : ?string {
        /** @var string|null $prefix */
        $prefix = Json::getParam('prefix', static::class);
        return $prefix;
    }

    /**
     * Helper to retrieve the json encoding.
     *
     * @return string|null
     */
    protected static function getJsonEncoding() : ?string {
        /** @var string|null $encoding */
        $encoding = Json::getParam('encoding', static::class);
        return $encoding;
    }

    /**
     * Helper to get the CBOR prefix.
     *
     * @return string|null
     */
    protected static function getCborPrefix() : ?string {
        /** @var string|null $cborPrefix */
        $cborPrefix = CBOR::getParam('prefix', static::class);
        return $cborPrefix;
    }

    /**
     * Helper to get the CBOR class name.
     *
     * @return string|null
     */
    protected static function getCborTarget() : ?string {
        /** @var string|null $cborTarget */
        $cborTarget = CBOR::getParam('target', static::class);
        return $cborTarget;
    }
}
