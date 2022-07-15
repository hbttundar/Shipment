<?php

declare(strict_types=1);

namespace App\DataTransformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\Dto\ShipmentOutput;
use App\Entity\Shipment;

class ShipmentOutputDataTransformer implements DataTransformerInterface
{
    /**
     * @param Shipment $shipment
     */
    public function transform($shipment, string $to, array $context = []): object
    {
        return ShipmentOutput::createFromEntity($shipment);
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return $data instanceof Shipment && $to === ShipmentOutput::class;
    }
}
