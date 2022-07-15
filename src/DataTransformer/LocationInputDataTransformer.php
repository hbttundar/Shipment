<?php

declare(strict_types=1);

namespace App\DataTransformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Dto\LocationInput;
use App\Entity\Location;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

class LocationInputDataTransformer implements DataTransformerInterface
{
    private ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @param LocationInput $input
     */
    public function transform($input, string $to, array $context = []):object
    {
        $this->validator->validate($input);

        $locationInput = $context[AbstractNormalizer::OBJECT_TO_POPULATE] ?? null;

        return $input->createOrUpdateEntity($locationInput);
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        if ($data instanceof Location) {
            // already transformed
            return false;
        }
        return $to === Location::class && ($context['input']['class'] ?? null) === LocationInput::class;
    }
}
