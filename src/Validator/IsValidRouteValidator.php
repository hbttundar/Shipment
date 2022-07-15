<?php

declare(strict_types=1);

namespace App\Validator;

use ApiPlatform\Core\Validator\ValidatorInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class IsValidRouteValidator extends ConstraintValidator
{


    private EntityManagerInterface $entityManager;
    private ValidatorInterface     $validator;

    public function __construct(EntityManagerInterface $entityManager, ValidatorInterface $validator)
    {
        $this->entityManager = $entityManager;
        $this->validator     = $validator;
    }

    public function validate(mixed $value, Constraint $constraint)
    {
        /* @var $constraint IsValidRoute */

        if (null === $value || '' === $value) {
            return;
        }

        if (!$value instanceof Collection) {
            throw new \LogicException('Route should be a collection with 2 items in it');
        }
        $errorList = [];
        if ($value->count() > 1) {
            $this->context->buildViolation($constraint->count_message)
                          ->addViolation();
        }

        if ($value->count() === 0) {
            $this->context->buildViolation($constraint->message)
                          ->addViolation();
        }

        foreach ($value as $location) {
            $errorList[] = $this->validator->validate($location, [new isValidLocation()]);
        }
        if ($errorList) {
            $this->context->buildViolation($constraint->message)
                          ->addViolation();
        }
    }

}