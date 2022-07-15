<?php

namespace App\Tests\Functional\Service;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Service\ShipmentPriceCalculatorFor1HundredKM;
use App\Service\ShipmentPriceCalculatorFor2HundredKM;

class ShipmentPriceCalculatorFor2HundredKMTest extends ApiTestCase
{
    /**
     * @test
     * @dataProvider   distanceDataProvider
     */
    public function it_can_calculate_price_Up_to_200_kilometer(float $distance, float $calculatedPrice): void
    {
        $defaultCalculator = new ShipmentPriceCalculatorFor1HundredKM();
        $priceCalculator   = new ShipmentPriceCalculatorFor2HundredKM($defaultCalculator);
        $price             = $priceCalculator->calculatePrice($distance)->getPrice();
        $this->assertSame($calculatedPrice, $price);
    }

    private function distanceDataProvider(): array
    {
        return [
            ["distance" => 101.0, "price" => 30.25],
            ["distance" => 150.0, "price" => 42.50],
            ["distance" => 173.0, "price" => 48.25],
            ["distance" => 190.0, "price" => 52.50],
            ["distance" => 200.0, "price" => 55.0],
        ];
    }
}