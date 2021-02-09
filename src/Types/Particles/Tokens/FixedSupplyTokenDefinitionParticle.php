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

use BN\BN;
use Techworker\RadixDLT\Serialization\FromJsonInterface;
use Techworker\RadixDLT\Serialization\ToJsonInterface;
use Techworker\RadixDLT\Types\Core\Address;
use Techworker\RadixDLT\Types\Core\RRI;
use Techworker\RadixDLT\Types\Core\String;
use Techworker\RadixDLT\Types\Core\EUID;
use Techworker\RadixDLT\Types\Core\UInt256;
use Techworker\RadixDLT\Types\Particles\AbstractParticle;

class FixedSupplyTokenDefinitionParticle extends AbstractParticle
    implements OwnableInterface, ToJsonInterface, FromJsonInterface
{
    protected function __construct(
        protected int $version,
        protected EUID $hid,
        protected array $destinations,
        protected RRI $rri,
        protected string $name,
        protected string $description,
        protected UInt256 $supply,
        protected UInt256 $granularity,
        protected string $iconUrl,
        protected string $url,
    ) {
        if ($granularity->getBn()->lt(new BN('0'))) {
            throw new \InvalidArgumentException('Granularity has to be larger than 0');
        }
    }

    public function getOwner(): Address
    {
        return $this->rri->getAddress();
    }

    public function getSymbol() : string {
        return $this->rri->getName();
    }


    public function getAddresses(): array
    {
        return [$this->rri->getAddress()->getUID()];
    }


    public static function fromJson(array|string $json): FixedSupplyTokenDefinitionParticle
    {
        if(is_string($json)) {
            throw new \InvalidArgumentException('Invalid.');
        }

        $version = (int)$json['version'];
        $hid = EUID::fromJson((string)$json['hid']);
        $destinations = [];
        /** @var string $destination */
        foreach((array)$json['destinations'] as $destination) {
            $destinations[] = EUID::fromJson("$destination");
        }
        $rri = RRI::fromJson((string)$json['rri']);
        $name = String::fromJson((string)$json['name']);
        $description = String::fromJson((string)$json['description']);
        $granularity = UInt256::fromJson((string)$json['granularity']);
        $supply = UInt256::fromJson((string)$json['supply']);
        $iconUrl = String::fromJson((string)$json['iconUrl']);
        $url = String::fromJson((string)$json['url']);

        return new self(
            version: $version,
            hid: $hid,
            destinations: $destinations,
            rri: $rri,
            name: $name,
            description: $description,
            supply: $supply,
            granularity: $granularity,
            iconUrl: $iconUrl,
            url: $url,
        );
    }

    public function toJson(): array|string
    {
        $json = [];
        $json['serializer'] = 'radix.particles.fixed_supply_token_definition';
        $json['version'] = $this->version;
        $json['hid'] = $this->hid->toJson();
        $json['destinations'] = array_map(fn(EUID $uid) => $uid->toJson(), $this->destinations);
        $json['rri'] = $this->rri->toJson();
        $json['name'] = $this->name->toJson();
        $json['description'] = $this->description->toJson();
        $json['granularity'] = $this->granularity->toJson();
        $json['iconUrl'] = $this->iconUrl->toJson();
        $json['url'] = $this->url->toJson();
        $json['supply'] = $this->supply->toJson();

        return $json;
    }
}
