<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Shipment;
use Symfony\Component\VarDumper\VarDumper;


class ShipmentPriceResolver
{
    private const KILOMETER_DIVISION_FACTOR = 1000;

    public function resolveShipmentPrice(Shipment $shipment): void
    {
        $distanceInKilometer = round($shipment->getDistance() / self::KILOMETER_DIVISION_FACTOR, 2);
        if (!$distanceInKilometer) {
            $shipment->setPrice(0.0);
            return;
        }
        $shipment->setPrice($this->getPriceCalculator($distanceInKilometer));
    }

    private function getPriceCalculator(float $distanceInKilometer): float
    {
        $defaultCalculator                   = new ShipmentPriceCalculatorFor1HundredKM();
        $calculatorServiceFor2Hundred        = new ShipmentPriceCalculatorFor2HundredKM($defaultCalculator);
        $calculatorServiceFor3Hundred        = new ShipmentPriceCalculatorFor3HundredKM($calculatorServiceFor2Hundred);
        $calculatorServiceForMorThan3Hundred = new ShipmentPriceCalculatorForMoreThan3HundredKM(
            $calculatorServiceFor3Hundred
        );
        return match (true) {
            $distanceInKilometer <= 100 => $defaultCalculator->calculatePrice($distanceInKilometer)->getPrice(),
            $distanceInKilometer > 100 && $distanceInKilometer <= 200 => $calculatorServiceFor2Hundred->calculatePrice(
                $distanceInKilometer
            )->getPrice(),
            $distanceInKilometer > 200 && $distanceInKilometer <= 300 => $calculatorServiceFor3Hundred->calculatePrice(
                $distanceInKilometer
            )->getPrice(),
            $distanceInKilometer > 300 => $calculatorServiceForMorThan3Hundred->calculatePrice(
                $distanceInKilometer
            )->getPrice(),
        };
    }


}