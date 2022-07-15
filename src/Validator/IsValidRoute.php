<?php

declare(strict_types=1);

namespace App\Validator;

use Doctrine\Common\Annotations\Annotation;
use Symfony\Component\Validator\Constraint;

#[\Attribute] #[Annotation]
class IsValidRoute extends Constraint
{
    public string $message = 'The route is not a valid route please check route collection items.';
    public string $count_message = 'The route should have only 2 items one as a source an another one as a destination.';


    public function validatedBy(): string
    {
        return IsValidRouteValidator::class;
    }
}