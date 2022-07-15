<?php

declare(strict_types=1);

namespace App\Dto;

use App\Entity\Location;
use Carbon\Carbon;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation as Serializer;

class LocationOutput
{
    #[Serializer\Groups(["shipment:read", "location:read"])]
    public string $postcode;

    #[Serializer\Groups(["shipment:read", "location:read"])]
    public string $city;

    #[Serializer\Groups(["shipment:read", "location:read"])]
    public string $country;

    #[Serializer\Groups(["location:read"])]
    public \DateTimeImmutable $createdAt;

    public static function createFromEntity(Location $location): self
    {
        $output            = new LocationOutput();
        $output->postcode  = $location->getPostcode();
        $output->city      = $location->getCity();
        $output->country   = $location->getCountry();
        $output->createdAt = $location->getCreatedAt();

        return $output;
    }


    #[Serializer\Groups(["location:read"])]
    public function getCreatedAt(): string
    {
        return Carbon::instance($this->createdAt)->diffForHumans();
    }
}
