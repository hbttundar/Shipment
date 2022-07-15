<?php

declare(strict_types=1);

namespace App\Dto;

use App\Entity\Carrier;
use App\Entity\Company;
use App\Entity\Shipment;
use Carbon\Carbon;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation as Serializer;

class ShipmentOutput
{
    #[Serializer\Groups(["shipment:read", "company:read", "carrier:read"])]
    public int $distance;

    #[Serializer\Groups(["shipment:read"])]
    public int $time;

    #[Serializer\Groups(["shipment:read", "company:read", "carrier:read"])]
    public float $price;

    #[Serializer\Groups(["shipment:read"])]
    public Company $company;

    #[Serializer\Groups(["shipment:read"])]
    public Carrier $carrier;

    #[Serializer\Groups(["shipment:read", "location:read"])]
    public Collection $route;

    #[Serializer\Groups(["shipment:read"])]
    public \DateTimeImmutable $createdAt;

    public static function createFromEntity(Shipment $shipment): self
    {
        $output            = new ShipmentOutput();
        $output->distance  = $shipment->getDistance();
        $output->time      = $shipment->getTime();
        $output->price     = $shipment->getPrice();
        $output->company   = $shipment->getCompany();
        $output->carrier   = $shipment->getCarrier();
        $output->route     = $shipment->getRoute();
        $output->createdAt = $shipment->getCreatedAt();

        return $output;
    }

    #[Serializer\Groups(["shipment:read"])]
    public function getCreatedAt(): string
    {
        return Carbon::instance($this->createdAt)->diffForHumans();
    }
}
