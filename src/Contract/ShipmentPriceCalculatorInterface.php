<?php

namespace App\Contract;

interface ShipmentPriceCalculatorInterface
{
    public function calculatePrice(float $distance): static;

    public function getPrice(): float;
}