<?php

declare(strict_types=1);

namespace App\Tests\Functional\Service;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Service\ShipmentPriceCalculatorFor1HundredKM;

class ShipmentPriceCalculatorFor1HundredKMTest extends ApiTestCase
{
    /**
     * @test
     * @dataProvider   distanceDataProvider
     */
    public function it_can_calculate_price_up_to_100_km(float $distance , float $calculatedPrice)
    {
        $priceCalculator = new ShipmentPriceCalculatorFor1HundredKM();
        $price = $priceCalculator->calculatePrice($distance)->getPrice();
        $this->assertSame($calculatedPrice,$price);
    }

    private function distanceDataProvider():array
    {
        return [
            ["distance"=>100.0 , "price"=> 30.0],
            ["distance"=>97.0 , "price"=> 29.1],
            ["distance"=>63.0 , "price"=> 18.9],
            ["distance"=>50.0 , "price"=> 15.0],
            ["distance"=>20.0 , "price"=> 6.0],
        ];
    }

}