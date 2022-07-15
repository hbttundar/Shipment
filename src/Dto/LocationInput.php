<?php

declare(strict_types=1);

namespace App\Dto;

use App\Entity\Location;

use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class LocationInput
{
    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Serializer\Groups(["location:read", "location:write", "shipment:item:get", "shipment:write"])]
    public ?string $postcode=null;

    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Serializer\Groups(["location:read", "location:write", "shipment:item:get", "shipment:write"])]
    public ?string $city=null;

    #[Assert\NotBlank(["groups"=>['create']])]
    #[Assert\Type("string")]
    #[Serializer\Groups(["location:read", "location:write", "shipment:item:get", "shipment:write"])]
    public ?string $country=null;

    public static function createFromEntity(?Location $location): self
    {
        $dto = new LocationInput();

        // not an edit, so just return an empty DTO
        if (!$location) {
            return $dto;
        }

        $dto->postcode  = $location->getPostcode();
        $dto->city      = $location->getCity();
        $dto->country   = $location->getCountry();

        return $dto;
    }

    public function createOrUpdateEntity(?Location $location): Location
    {
        if (!$location) {
            $location = new Location();
        }
        $location->setPostcode($this->postcode);
        $location->setCity($this->city);
        $location->setCountry($this->country);

        return $location;
    }
}
