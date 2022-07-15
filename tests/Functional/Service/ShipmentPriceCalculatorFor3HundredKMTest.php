<?php

namespace App\Tests\Functional\Service;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Service\ShipmentPriceCalculatorFor1HundredKM;
use App\Service\ShipmentPriceCalculatorFor2HundredKM;
use App\Service\ShipmentPriceCalculatorFor3HundredKM;

class ShipmentPriceCalculatorFor3HundredKMTest extends ApiTestCase
{

    /**
     * @test
     * @dataProvider   distanceDataProvider
     */
    public function it_can_calculate_price_Up_to_300_kilometer(float $distance, float $calculatedPrice): void
    {
        $defaultCalculator = new ShipmentPriceCalculatorFor1HundredKM();
        $TwoHundredKmPriceCalculator   = new ShipmentPriceCalculatorFor2HundredKM($defaultCalculator);
        $priceCalculator   = new ShipmentPriceCalculatorFor3HundredKM($TwoHundredKmPriceCalculator);
        $price             = $priceCalculator->calculatePrice($distance)->getPrice();
        $this->assertSame($calculatedPrice, $price);
    }

    private function distanceDataProvider(): array
    {
        return [
            ["distance" => 201.0, "price" => 55.20],
            ["distance" => 250.0, "price" => 65.0],
            ["distance" => 283.0, "price" => 71.60],
            ["distance" => 290.0, "price" => 73.0],
            ["distance" => 300.0, "price" => 75.0],
        ];
    }

}