<?php

declare(strict_types=1);

namespace App\Service;

use App\Contract\ShipmentPriceCalculatorInterface;

class ShipmentPriceCalculatorForMoreThan3HundredKM
{

    private const PRICE_PER_KILOMETER = 0.15;

    private ShipmentPriceCalculatorInterface $priceCalculator;
    private float                            $price;

    public function __construct(ShipmentPriceCalculatorInterface $priceCalculator)
    {
        $this->priceCalculator = $priceCalculator;
        return $this;
    }

    public function calculatePrice(float $distance): static
    {
        $this->price       = $this->priceCalculator->calculatePrice($distance)->getPrice();
        $effectiveDistance = $distance - (
                ShipmentPriceCalculatorFor1HundredKM::BASE_AMOUNT_OF_KILOMETERS +
                ShipmentPriceCalculatorFor1HundredKM::BASE_AMOUNT_OF_KILOMETERS +
                ShipmentPriceCalculatorFor1HundredKM::BASE_AMOUNT_OF_KILOMETERS
            );
        $this->price       += round($effectiveDistance * self::PRICE_PER_KILOMETER, 2);
        return $this;
    }

    public function getPrice(): float
    {
        return $this->price;
    }
}