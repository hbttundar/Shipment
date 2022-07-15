<?php

declare(strict_types=1);

namespace App\Dto;

use App\Entity\Carrier;
use Carbon\Carbon;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation as Serializer;

class CarrierOutput
{
    #[Serializer\Groups(["shipment:read", "carrier:read"])]
    public string $name;

    #[Serializer\Groups(["shipment:read", "carrier:read"])]
    public string $email;

    #[Serializer\Groups(["carrier:read"])]
    public \DateTimeImmutable $createdAt;

    public Collection $shipments;

    public static function createFromEntity(carrier $carrier): self
    {
        $output            = new CarrierOutput();
        $output->name      = $carrier->getName();
        $output->email     = $carrier->getEmail();
        $output->shipments  = $carrier->getShipments();
        $output->createdAt = $carrier->getCreatedAt();

        return $output;
    }


    #[Serializer\Groups(["company:read"])]
    public function getCreatedAt(): string
    {
        return Carbon::instance($this->createdAt)->diffForHumans();
    }
}
