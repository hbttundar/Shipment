<?php

declare(strict_types=1);

namespace App\Dto;

use App\Entity\Company;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class CompanyInput
{
    #[Assert\NotBlank(["groups" => ['create']])]
    #[Serializer\Groups(["shipment:write", "company:read", "company:write", "shipment:item:get", "shipment:write"])]
    public ?string $name = null;

    #[Assert\NotBlank(["groups" => ['create']])]
    #[Assert\Email]
    #[Serializer\Groups(["shipment:write", "company:read", "company:write", "shipment:item:get", "shipment:write"])]
    public ?string $email = null;

    public static function createFromEntity(?Company $company): self
    {
        $dto = new CompanyInput();

        // not an edit, so just return an empty DTO
        if (!$company) {
            return $dto;
        }

        $dto->name  = $company->getName();
        $dto->email = $company->getEmail();

        return $dto;
    }

    public function createOrUpdateEntity(?Company $company): Company
    {
        if (!$company) {
            $company = new Company();
        }
        $company->setname($this->name);
        $company->setEmail($this->email);

        return $company;
    }
}
