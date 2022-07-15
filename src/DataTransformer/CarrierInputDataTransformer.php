<?php

declare(strict_types=1);

namespace App\DataTransformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Dto\CarrierInput;
use App\Entity\Carrier;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

class CarrierInputDataTransformer implements DataTransformerInterface
{
    private ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @param CarrierInput $input
     */
    public function transform($input, string $to, array $context = [])
    {
        $this->validator->validate($input);

        $cheeseListing = $context[AbstractNormalizer::OBJECT_TO_POPULATE] ?? null;

        return $input->createOrUpdateEntity($cheeseListing);
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        if ($data instanceof Carrier) {
            // already transformed
            return false;
        }
        return $to === Carrier::class && ($context['input']['class'] ?? null) === CarrierInput::class;
    }
}
