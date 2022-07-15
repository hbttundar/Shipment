<?php

namespace App\Factory;

use App\Entity\Company;
use App\Repository\CompanyRepository;
use JetBrains\PhpStorm\ArrayShape;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @method static Company|Proxy findOrCreate(array $attributes)
 * @method static Company|Proxy random()
 * @method static Company[]|Proxy[] randomSet(int $number)
 * @method static Company[]|Proxy[] randomRange(int $min, int $max)
 * @method static CompanyRepository|RepositoryProxy repository()
 * @method Company|Proxy create($attributes = [])
 * @method Company[]|Proxy[] createMany(int $number, $attributes = [])
 */
class CompanyFactory extends ModelFactory
{

    protected static function getClass(): string
    {
        return Company::class;
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