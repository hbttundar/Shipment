<?php

declare(strict_types=1);

namespace App\DataTransformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\Dto\CompanyOutput;
use App\Entity\Company;


class CompanyOutputDataTransformer implements DataTransformerInterface
{
    /**
     * @param Company $carrier
     */
    public function transform($carrier, string $to, array $context = [])
    {
        return CompanyOutput::createFromEntity($carrier);
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return $data instanceof Company && $to === CompanyOutput::class;
    }
}
