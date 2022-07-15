<?php

declare(strict_types=1);

namespace App\Dto;

use App\Entity\Carrier;
use App\Entity\Company;
use App\Entity\Shipment;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class ShipmentInput
{
    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Positive]
    #[Serializer\Groups(["shipment:write", "company:write", "carrier:write"])]
    public ?int $distance = null;

    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Positive]
    #[Serializer\Groups(["shipment:write", "company:write", "carrier:write"])]
    public ?int $time = null;

    #[Serializer\Ignore]
    public ?float $price = null;

    #[Assert\Valid]
    #[Assert\NotNull]
    #[Assert\NotBlank]
    #[Serializer\Groups(["shipment:collection:post"])]
    public ?Company $company = null;

    #[Assert\Valid]
    #[Assert\NotNull]
    #[Assert\NotBlank]
    #[Serializer\Groups(["shipment:collection:post"])]
    public ?Carrier $carrier = null;

    #[Assert\Valid]
    #[Assert\NotNull]
    #[Assert\NotBlank]
    #[Serializer\Groups(["shipment:write", "location:write", "location:write"])]
    public ?Collection $route = null;

    public static function createFromEntity(?Shipment $shipment): self
    {
        $dto = new ShipmentInput();

        // not an edit, so just return an empty DTO
        if (!$shipment) {
            return $dto;
        }
        $dto->distance = $shipment->getDistance();
        $dto->time     = $shipment->getTime();
        $dto->company  = $shipment->getCompany();
        $dto->carrier  = $shipment->getCarrier();
        $dto->route    = $shipment->getRoute();
        return $dto;
    }

    public function createOrUpdateEntity(?Shipment $shipment): Shipment
    {
        if (!$shipment) {
            $shipment = new Shipment();
        }
        $shipment->setDistance($this->distance);
        $shipment->setTime($this->time);
        $shipment->setCompany($this->company);
        $shipment->setCarrier($this->carrier);
        foreach ($this->route as $routeLocation) {
            $shipment->addRoute($routeLocation);
        }

        return $shipment;
    }
}
