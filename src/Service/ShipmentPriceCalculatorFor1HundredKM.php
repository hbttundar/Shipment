<?php

declare(strict_types=1);

namespace App\Service;

use App\Contract\ShipmentPriceCalculatorInterface;

class ShipmentPriceCalculatorFor1HundredKM implements ShipmentPriceCalculatorInterface
{
    public const  BASE_AMOUNT_OF_KILOMETERS = 100;

    private const PRICE_FOR_FIRST_HUNDRED_KM = 30.0;
    private const PRICE_PER_KILOMETER        = 0.30;

    private float $price = 0.0;

    public function calculatePrice(float $distance): static
    {
        if ($distance >= self::BASE_AMOUNT_OF_KILOMETERS) {
            $this->price = self::PRICE_FOR_FIRST_HUNDRED_KM;
            return $this;
        }
        $this->price = round($distance * self::PRICE_PER_KILOMETER , 2);
        return $this;
    }

    public function getPrice(): float
    {
        return ($this->price);
    }


}