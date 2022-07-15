<?php

declare(strict_types=1);

namespace App\Dto;

use App\Entity\Carrier;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class CarrierInput
{
    #[Assert\NotBlank(["groups" => ['create']])]
    #[Serializer\Groups(["shipment:write", "carrier:read", "carrier:write", "shipment:item:get", "shipment:write"])]
    public ?string $name = null;

    #[Assert\NotBlank(["groups" => ['create']])]
    #[Assert\Email]
    #[Serializer\Groups(["shipment:write", "carrier:read", "carrier:write", "shipment:item:get", "shipment:write"])]
    public ?string $email = null;


    public static function createFromEntity(?Carrier $carrier): self
    {
        $dto = new CarrierInput();

        // not an edit, so just return an empty DTO
        if (!$carrier) {
            return $dto;
        }

        $dto->name  = $carrier->getName();
        $dto->email = $carrier->getEmail();

        return $dto;
    }

    public function createOrUpdateEntity(?Carrier $carrier): Carrier
    {
        if (!$carrier) {
            $carrier = new Carrier();
        }
        $carrier->setname($this->name);
        $carrier->setEmail($this->email);

        return $carrier;
    }
}
