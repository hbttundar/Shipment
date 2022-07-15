<?php

namespace App\Factory;

use App\Entity\Carrier;
use App\Repository\CarrierRepository;
use JetBrains\PhpStorm\ArrayShape;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @method static Carrier|Proxy findOrCreate(array $attributes)
 * @method static Carrier|Proxy random()
 * @method static Carrier[]|Proxy[] randomSet(int $number)
 * @method static Carrier[]|Proxy[] randomRange(int $min, int $max)
 * @method static CarrierRepository|RepositoryProxy repository()
 * @method Carrier|Proxy create($attributes = [])
 * @method Carrier[]|Proxy[] createMany(int $number, $attributes = [])
 */
class CarrierFactory extends ModelFactory
{

    protected static function getClass(): string
    {
        return Carrier::class;
    }

    protected function getDefaults(): array
    {
        return [
            'name'  => self::faker()->company,
            'email'     => self::faker()->email,
            'createdAt' => new \DateTimeImmutable()
        ];
    }

    protected function initialize(): self
    {
        return $this;
    }
}