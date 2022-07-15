<?php

declare(strict_types=1);

namespace App\Validator;

use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Entity\Company;
use App\Entity\Location;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintValidator;

class IsValidLocationValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint)
    {
        /* @var $constraint IsValidLocation */

        if (null === $value || '' === $value) {
            return;
        }

        if (!$value instanceof Location) {
            throw new \LogicException('Only Location is supported');
        }

        $postCode = $value->getPostcode();
        $city     = $value->getCity();
        $country  = $value->getCountry();

        if ($postCode === null || trim($postCode) === '') {
            $this->context->buildViolation($constraint->message)
                          ->setParameter('{{ value }}', $postCode)
                          ->setParameter('{{ name }}', "postCode")
                          ->addViolation();
        }
        if ($city === null || trim($city) === '') {
            $this->context->buildViolation($constraint->message)
                          ->setParameter('{{ value }}', $city)
                          ->setParameter('{{ name }}', "city")
                          ->addViolation();
        }
        if ($country === null || trim($country) === '') {
            $this->context->buildViolation($constraint->message)
                          ->setParameter('{{ value }}', $country)
                          ->setParameter('{{ name }}', "country")
                          ->addViolation();
        }
    }
}