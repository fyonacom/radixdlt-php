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

namespace Techworker\RadixDLT\Types\Particles\Tokens;

use Techworker\RadixDLT\Types\Primitives\String_;

class TokenPermission
{
    public const TOKEN_OWNER_ONLY = 'token_owner_only';

    public const ALL = 'all';

    public const NONE = 'none';

    public function __construct(
        protected String_ $mint,
        protected String_ $burn
    ) {
    }

    public static function fromJson(array | string $json): self
    {
        if (is_string($json)) {
            throw new \InvalidArgumentException('fdnjfs');
        }

        return new self(
            String_::fromJson((string) $json['mint']),
            String_::fromJson((string) $json['burn'])
        );
    }

    public function toJson(): array | string
    {
        $json = [];
        $json['mint'] = $this->mint->toJson();
        $json['burn'] = $this->burn->toJson();
        return $json;
    }
}
