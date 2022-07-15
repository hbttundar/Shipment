<?php

declare(strict_types=1);

namespace App\Dto;

use App\Entity\Company;
use Carbon\Carbon;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation as Serializer;

class CompanyOutput
{
    #[Serializer\Groups(["shipment:read", "company:read"])]
    public string $name;

    #[Serializer\Groups(["shipment:read", "company:read"])]
    public string $email;

    #[Serializer\Groups(["company:read"])]
    public \DateTimeImmutable $createdAt;

    public Collection $shipments;


    public static function createFromEntity(Company $company): self
    {
        $output            = new CompanyOutput();
        $output->name      = $company->getName();
        $output->email     = $company->getEmail();
        $output->createdAt = $company->getCreatedAt();
        $output->shipments  = $company->getShipments();

        return $output;
    }


    #[Serializer\Groups(["company:read"])]
    public function getCreatedAt(): string
    {
        return Carbon::instance($this->createdAt)->diffForHumans();
    }
}
