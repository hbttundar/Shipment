<?php

declare(strict_types=1);

namespace App\DataTransformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\Dto\LocationOutput;
use App\Entity\Location;

class LocationOutputDataTransformer implements DataTransformerInterface
{
    /**
     * @param Location $carrier
     */
    public function transform($carrier, string $to, array $context = [])
    {
        return LocationOutput::createFromEntity($carrier);
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return $data instanceof Location && $to === LocationOutput::class;
    }
}
