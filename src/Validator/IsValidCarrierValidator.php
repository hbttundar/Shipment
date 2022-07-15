<?php

declare(strict_types=1);

namespace App\Validator;

use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Entity\Carrier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintValidator;

class IsValidCarrierValidator extends ConstraintValidator
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
        /* @var $constraint IsValidCarrier */

        if (null === $value || '' === $value) {
            return;
        }

        if (!$value instanceof Carrier) {
            throw new \LogicException('Only Carrier is supported');
        }

        $name  = $value->getName();
        $email = $value->getEmail();
        if ($name === null || trim($name) === '') {
            $this->context->buildViolation($constraint->message)
                          ->setParameter('{{ value }}', $name)
                          ->setParameter('{{ name }}', "name")
                          ->addViolation();
        }
        $errorList = $this->validator->validate($email, [new Email(), new NotBlank()]);
        if ($errorList) {
            $this->context->buildViolation($constraint->message)
                          ->setParameter('{{ value }}', $name)
                          ->setParameter('{{ name }}', "name")
                          ->addViolation();
        }
    }

}