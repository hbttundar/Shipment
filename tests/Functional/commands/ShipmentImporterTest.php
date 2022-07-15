<?php

declare(strict_types=1);

namespace App\Tests\Functional\commands;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Shipment;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class ShipmentImporterTest extends ApiTestCase
{
    private const PRICE_FOR_ONE_HUNDRED_KILOMETER   = 30.0;
    public const PRICE_FOR_TWO_HUNDRED_KILOMETER   = 55.0;
    private const PRICE_FOR_THREE_HUNDRED_KILOMETER = 75.0;

    private EntityManager $entityManager;
    private CommandTester $commandTester;

    protected function setUp(): void
    {
        $kernel              = self::bootKernel(["environment" => "test", "debug" => true]);
        $application         = new Application($kernel);
        $this->entityManager = $kernel->getContainer()
                                      ->get('doctrine')
                                      ->getManager();
        $command             = $application->find('shipment:import');
        $this->commandTester = new CommandTester($command);
    }

    /** @test */
    public function it_can_import_shipment_json_file(): void
    {
        //given shipments json file
        $file = 'tests/Helper/shipments.json';
        if(!file_exists($file)){
            self::throwException();
        }
        // when shipmentImport command execute
        $this->commandTester->execute(['shipment_file' => $file]);
        $count = $this->entityManager->getRepository(Shipment::class)->count([]);
        $this->assertSame(4, $count);
        /** @var Shipment $shipment */
        $shipment = $this->entityManager->getRepository(Shipment::class)->findOneByCompanyName('Ms. Pansy Tremblay');
        $this->assertNotNull($shipment);
        $this->assertSame("Ms. Pansy Tremblay", $shipment->getCompany()->getName());
        $shipment = $this->entityManager->getRepository(Shipment::class)->findOneByCompanyName("HBT");
        $this->assertNull($shipment);
    }

    /** @test */
    public function it_can_import_range_of_data_from_provided_json_file(): void
    {
        //given shipments json file
        $file = 'tests/Helper/shipments.json';
        // when shipmentImport command execute
        $this->commandTester->execute([
            'shipment_file' => $file,
            '--from'        => 0,
            '--to'          => 2
        ]);
        $count = $this->entityManager->getRepository(Shipment::class)->count([]);
        $this->assertSame(3, $count);
        /** @var Shipment $shipment */
        $shipment = $this->entityManager->getRepository(Shipment::class)->findOneByCompanyName('Ms. Pansy Tremblay');
        $this->assertNotNull($shipment);
        $this->assertSame("Ms. Pansy Tremblay", $shipment->getCompany()->getName());
        /** @var Shipment $shipment */
        $shipment = $this->entityManager->getRepository(Shipment::class)->findOneByCompanyName('Prof. Maryjane Koepp');
        $this->assertNull($shipment);
    }


    /**
     * @test
     * @dataProvider  priceCalculatorDataProvider
     */
    public function it_update_price_based_on_distance_during_import(string $companyName, float $shipmentPrice): void
    {
        //given shipments json file
        $file = 'tests/Helper/shipments.json';
        // when shipmentImport command execute
        $this->commandTester->execute(['shipment_file' => $file]);
        // when shipmentImport command execute
        /** @var Shipment $shipment */
        $shipment = $this->entityManager->getRepository(Shipment::class)->findOneByCompanyName($companyName);
        $this->assertNotNull($shipment);
        $this->assertNotNull($shipment->getPrice());
        $this->assertSame($shipmentPrice, $shipment->getPrice());
    }

    private function priceCalculatorDataProvider(): array
    {
        return [
            "100km" => [
                "companyName" => 'Ms. Pansy Tremblay',
                "shipmentPrice"       => self::PRICE_FOR_ONE_HUNDRED_KILOMETER
            ],
            "200km" => [
                "companyName" => "Sam Feil",
                "shipmentPrice"       => self::PRICE_FOR_TWO_HUNDRED_KILOMETER
            ],
            "300km" => [
                "companyName" => "Lesly Veum Jr.",
                "shipmentPrice"       => self::PRICE_FOR_THREE_HUNDRED_KILOMETER
            ],

            "578,97km" => [
                "companyName" => "Prof. Maryjane Koepp",
                "shipmentPrice"       => 116.85
            ],
        ];
    }

}