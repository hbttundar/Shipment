<?php

namespace App\Factory;

use App\Entity\Company;
use App\Entity\Location;
use App\Repository\CompanyRepository;
use App\Repository\LocationRepository;
use JetBrains\PhpStorm\ArrayShape;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @method static Location|Proxy findOrCreate(array $attributes)
 * @method static Location|Proxy random()
 * @method static Location[]|Proxy[] randomSet(int $number)
 * @method static Location[]|Proxy[] randomRange(int $min, int $max)
 * @method static LocationRepository|RepositoryProxy repository()
 * @method Location|Proxy create($attributes = [])
 * @method Location[]|Proxy[] createMany(int $number, $attributes = [])
 */
class LocationFactory extends ModelFactory
{

    protected static function getClass(): string
    {
        return Location::class;
    }

    protected function getDefaults(): array
    {
        return [
            'postcode'  => self::faker()->postcode,
            'city'      => self::faker()->city,
            'country'   => self::faker()->countryCode,
            'createdAt' => new \DateTimeImmutable()
        ];
    }

    protected function initialize(): self
    {
        return $this;
    }
}