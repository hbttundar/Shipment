<?php

declare(strict_types=1);

namespace App\Validator;

use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Entity\Shipment;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\ConstraintViolation;

class IsValidShipmentValidator extends ConstraintValidator
{
    private ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function validate(mixed $value, Constraint $constraint)
    {
        if (null === $value || '' === $value) {
            return;
        }

        if (!$value instanceof Shipment) {
            throw new \LogicException('Only Shipment is supported');
        }

        $company = $value->getCompany();
        $carrier = $value->getCarrier();
        $route   = $value->getLocations();


        $violations[] = $this->validator->validate($company, [new IsValidCompany()]);
        $violations[] = $this->validator->validate($carrier, [new IsValidCarrier()]);
        $violations[] = $this->validator->validate($route, [new IsValidRoute()]);
        /** @var ConstraintViolation $violation */
        foreach ($violations as $violation) {
            $this->context->buildViolation($constraint->message)
                          ->addViolation($violation);
        }
    }
}