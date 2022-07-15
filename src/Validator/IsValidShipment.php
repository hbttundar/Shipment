<?php

declare(strict_types=1);

namespace App\Validator;

use Doctrine\Common\Annotations\Annotation;
use Symfony\Component\Validator\Constraint;

#[\Attribute] #[Annotation]
class IsValidShipment extends Constraint
{
    public string $message = 'The shipment is not valid';

    public function validatedBy(): string
    {
        return IsValidShipmentValidator::class;
    }

    public function getTargets(): array|string
    {
        return self::CLASS_CONSTRAINT;
    }
}