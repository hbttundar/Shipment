<?php

declare(strict_types=1);

namespace App\DataTransformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\Dto\CarrierOutput;
use App\Entity\Carrier;

class CarrierOutputDataTransformer implements DataTransformerInterface
{
    /**
     * @param carrier $carrier
     */
    public function transform($carrier, string $to, array $context = [])
    {
        return CarrierOutput::createFromEntity($carrier);
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return $data instanceof Carrier && $to === CarrierOutput::class;
    }
}
