<?php

declare(strict_types=1);

namespace App\DataTransformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Dto\CompanyInput;
use App\Entity\Company;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;


class CompanyInputDataTransformer implements DataTransformerInterface
{
    private ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @param CompanyInput $input
     */
    public function transform($input, string $to, array $context = [])
    {
        $this->validator->validate($input);

        $companyInput = $context[AbstractNormalizer::OBJECT_TO_POPULATE] ?? null;

        return $input->createOrUpdateEntity($companyInput);
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        if ($data instanceof Company) {
            // already transformed
            return false;
        }
        return $to === Company::class && ($context['input']['class'] ?? null) === CompanyInput::class;
    }
}
