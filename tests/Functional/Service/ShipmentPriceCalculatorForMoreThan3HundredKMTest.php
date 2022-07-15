<?php

namespace App\Tests\Functional\Service;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Service\ShipmentPriceCalculatorFor1HundredKM;
use App\Service\ShipmentPriceCalculatorFor2HundredKM;
use App\Service\ShipmentPriceCalculatorFor3HundredKM;
use App\Service\ShipmentPriceCalculatorForMoreThan3HundredKM;

class ShipmentPriceCalculatorForMoreThan3HundredKMTest extends ApiTestCase
{
    /**
     * @test
     * @dataProvider   distanceDataProvider
     */
    public function it_can_calculate_price_from_301_kilometer(float $distance, float $calculatedPrice): void
    {
        $defaultCalculator             = new ShipmentPriceCalculatorFor1HundredKM();
        $twoHundredKmPriceCalculator   = new ShipmentPriceCalculatorFor2HundredKM($defaultCalculator);
        $threeHundredKmPriceCalculator = new ShipmentPriceCalculatorFor3HundredKM($twoHundredKmPriceCalculator);
        $priceCalculator               = new ShipmentPriceCalculatorForMoreThan3HundredKM(
            $threeHundredKmPriceCalculator
        );
        $price = $priceCalculator->calculatePrice($distance)->getPrice();
        $this->assertSame($calculatedPrice, $price);
    }

    private function distanceDataProvider(): array
    {
        return [
            ["distance" => 301.0, "price" => 75.15],
            ["distance" => 354.0, "price" => 83.10],
            ["distance" => 383.0, "price" => 87.45],
            ["distance" => 390.0, "price" => 88.50],
            ["distance" => 400.0, "price" => 90.0],
        ];
    }

}