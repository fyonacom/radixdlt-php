<?php

namespace Techworker\RadixDLT\Serialization;

interface EncodingType
{
    public const CUSTOM = 'custom';

    public const HEX = 'hex';

    public const BIN = 'bin';

    public const STR = 'bin';

    public const BASE58 = 'base58';

    public const BASE64 = 'base64';

    public const UINT256 = 'uint256';
}
