<?php

declare(strict_types=1);

namespace App\Validator;

use Doctrine\Common\Annotations\Annotation;
use Symfony\Component\Validator\Constraint;

#[\Attribute] #[Annotation]
class IsValidCompany extends Constraint
{
    public string $message = 'The {{ name }} "{{ value }}" is not a valid {{ name }}.';


    public function validatedBy(): string
    {
        return IsValidCompanyValidator::class;
    }

}