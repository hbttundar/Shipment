<?php

declare(strict_types=1);

namespace App\DataTransformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Dto\ShipmentInput;
use App\Entity\Shipment;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

class ShipmentInputDataTransformer implements DataTransformerInterface
{
    private ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @param ShipmentInput $input
     */
    public function transform($input, string $to, array $context = []): object
    {
        $this->validator->validate($input);

        $shipmentInput = $context[AbstractNormalizer::OBJECT_TO_POPULATE] ?? null;

        return $input->createOrUpdateEntity($shipmentInput);
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        if ($data instanceof Shipment) {
            // already transformed
            return false;
        }
        return $to === Shipment::class && ($context['input']['class'] ?? null) === ShipmentInput::class;
    }
}
