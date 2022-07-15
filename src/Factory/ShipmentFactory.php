<?php

namespace App\Factory;

use App\Entity\Location;
use App\Entity\Shipment;
use App\Repository\LocationRepository;
use App\Repository\ShipmentRepository;
use App\Service\ShipmentPriceResolver;
use ContainerBQx9CpS\get_Console_Command_TranslationDebug_LazyService;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @method static Shipment|Proxy findOrCreate(array $attributes)
 * @method static Shipment|Proxy random()
 * @method static Shipment[]|Proxy[] randomSet(int $number)
 * @method static Shipment[]|Proxy[] randomRange(int $min, int $max)
 * @method static ShipmentRepository|RepositoryProxy repository()
 * @method Shipment|Proxy create($attributes = [])
 * @method Shipment[]|Proxy[] createMany(int $number, $attributes = [])
 */
class ShipmentFactory extends ModelFactory
{
    protected static function getClass(): string
    {
        return Shipment::class;
    }

    protected function getDefaults(): array
    {
        return [
            'distance'  => self::faker()->numberBetween(58000),
            'time'      => self::faker()->numberBetween(5, 40),
            'price'     => self::faker()->randomDigit(),
            'company'   => CompanyFactory::new(),
            'carrier'   => CarrierFactory::new(),
            'route'     => [
                LocationFactory::new(),
                LocationFactory::new()
            ],
            'createdAt' => new \DateTimeImmutable()
        ];
    }

    protected function initialize(): self
    {
        return $this;
    }
}