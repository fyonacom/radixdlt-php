<?php

namespace Techworker\RadixDLT\Types\Particles\Tokens;

use SebastianBergmann\CodeCoverage\Driver\Selector;
use Techworker\RadixDLT\Serialization\FromJsonInterface;
use Techworker\RadixDLT\Serialization\ToJsonInterface;
use Techworker\RadixDLT\Types\Core\String;

class TokenPermission implements ToJsonInterface, FromJsonInterface {
    public const TOKEN_OWNER_ONLY = 'token_owner_only';
    public const ALL = 'all';
    public const NONE = 'none';

    public function __construct(protected string $mint,
                                protected string $burn)
    {
    }

    public static function fromJson(array|string $json): TokenPermission
    {
        if(is_string($json)) {
            throw new \InvalidArgumentException('fdnjfs');
        }

        return new self(
            String::fromJson((string)$json['mint']),
            String::fromJson((string)$json['burn'])
        );
    }

    public function toJson(): array|string
    {
        $json = [];
        $json['mint'] = $this->mint->toJson();
        $json['burn'] = $this->burn->toJson();
        return $json;
    }


}
