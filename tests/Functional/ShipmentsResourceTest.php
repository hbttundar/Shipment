<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Carrier;
use App\Entity\Company;
use App\Entity\Location;
use App\Entity\Shipment;
use App\Factory\CarrierFactory;
use App\Factory\CompanyFactory;
use App\Factory\LocationFactory;
use App\Factory\ShipmentFactory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ShipmentsResourceTest extends ApiTestCase
{
    private HttpClientInterface $client;

    protected function setUp(): void
    {
        $this->client = self::createClient();
    }

    /** @test */
    public function it_can_return_collection_of_shipments()
    {
        $company       = CompanyFactory::createOne(
            [
                'name'  => 'HBO',
                'email' => 'info@hbo.com'
            ]
        );
        $carrier       = CarrierFactory::createOne(
            [
                'name'  => 'OBH',
                'email' => 'info@obh.com'
            ]
        );
        $from_location = LocationFactory::createOne(
            [
                'postcode' => '14793',
                'city'     => 'Berlin',
                'country'  => 'DE'
            ]
        );
        $to_location   = LocationFactory::createOne(
            [
                'postcode' => '14968',
                'city'     => 'Hamburg',
                'country'  => 'DE'
            ]
        );
        $shipment      = ShipmentFactory::createOne(
            [
                'distance' => 200000,
                'time'     => 2,
                'price'    => 55.0,
                'company'  => $company,
                'carrier'  => $carrier,
                'route'    => [
                    $from_location,
                    $to_location
                ]
            ]
        );
        ShipmentFactory::createMany(10);
        $this->client->request('GET', '/api/shipments');
        $this->assertJsonContains(['hydra:totalItems' => 11]);
        $this->assertJsonContains(
            [
                'hydra:member' => [
                    0 => [
                        '@type'     => 'Shipment',
                        '@id'       => '/api/shipments/' . $shipment->getId(),
                        'distance'  => 200000,
                        'time'      => 2,
                        'price'    => 55,
                        'createdAt' => '1 second ago',
                    ]
                ]
            ],
        );
    }

    /** @test */
    public function it_can_validate_json_body_for_create_new_shipment()
    {
        $this->client->request('POST', '/api/shipments', [
            'json' => [],
        ]);
        $this->assertResponseStatusCodeSame(422);
    }

    /**
     * @test
     * @dataProvider shipmentsDataProvider
     */
    public function it_can_create_shipments(array $company, array $carrier, array $route)
    {
        $this->client->request(
            'POST',
            '/api/shipments',
            [
                'json' => [
                    'distance' => 100000,
                    'time'     => 1,
                    'company'  => $company,
                    'carrier'  => $carrier,
                    'route'    => $route
                ]
            ]
        );
        $this->assertResponseStatusCodeSame(201);
        $entityManager = self::getEntityManager();
        /** @var Shipment $shipment */
        $shipment = $entityManager->getRepository(Shipment::class)->findOneByCompanyName('HBO');
        $this->assertNotNull($shipment);
        $this->assertSame(100000, $shipment->getDistance());

        $this->client->request(
            'POST',
            '/api/shipments',
            [
                'json' => [
                    'distance' => 400000,
                    'time'     => 1,
                    'company'  => $company,
                    'carrier'  => $carrier,
                    'route'    => $route
                ]
            ]
        );
        $this->assertResponseStatusCodeSame(201);
        $shipments = $entityManager->getRepository(Shipment::class)->findAll();
        $this->assertCount(2, $shipments);
    }

    /**
     * @test
     * @dataProvider shipmentsDataProvider
     */
    public function it_prevent_duplication_when_create_new_shipment(array $company, array $carrier, array $route)
    {
        $this->client->request(
            'POST',
            '/api/shipments',
            [
                'json' => [
                    'distance' => 100000,
                    'time'     => 1,
                    'company'  => $company,
                    'carrier'  => $carrier,
                    'route'    => $route
                ]
            ]
        );
        $this->assertResponseStatusCodeSame(201);
        $this->client->request(
            'POST',
            '/api/shipments',
            [
                'json' => [
                    'distance' => 500000,
                    'time'     => 5,
                    'company'  => $company,
                    'carrier'  => $carrier,
                    'route'    => $route
                ]
            ]
        );
        $this->assertResponseStatusCodeSame(201);
        $this->client->request(
            'POST',
            '/api/shipments',
            [
                'json' => [
                    'distance' => 600000,
                    'time'     => 6,
                    'company'  => $company,
                    'carrier'  => $carrier,
                    'route'    => $route
                ]
            ]
        );
        $entityManager = self::getEntityManager();
        $this->assertResponseStatusCodeSame(201);
        $companies = $entityManager->getRepository(Company::class)->findAll();
        $this->assertCount(1, $companies);
        $this->assertSame($companies[0]->getName(), $company['name']);
        $this->assertSame($companies[0]->getEmail(), $company['email']);

        $carriers = $entityManager->getRepository(Carrier::class)->findAll();
        $this->assertCount(1, $carriers);
        $this->assertSame($carriers[0]->getName(), $carrier['name']);
        $this->assertSame($carriers[0]->getEmail(), $carrier['email']);
        $locations = $entityManager->getRepository(Location::class)->findAll();
        $this->assertCount(2, $locations);
        $index = 0;
        foreach ($locations as $location) {
            $this->assertContains($location->getPostcode(), $route[$index]);
            $this->assertContains($location->getCity(), $route[$index]);
            $this->assertContains($location->getCountry(), $route[$index]);
            $index++;
        }
    }


    /** @test */
    public function it_can_validate_shipment_data_for_update()
    {
        $company  = CompanyFactory::new()->create();
        $shipment = ShipmentFactory::new()->create(['company' => $company]);

        // 1) the distance must be a positive number
        $this->client->request('PUT', '/api/shipments/' . $shipment->getId(), [
            'json' => [
                'distance' => 0,
                'time'     => $shipment->getTime(),
                'company'  => $shipment->getCompany(),
                'carrier'  => $shipment->getCarrier(),
                $shipment->getRoute()
            ]
        ]);
        $this->assertResponseStatusCodeSame(422, 'distance: This value should be positive');
    }

    protected static function getEntityManager(): EntityManagerInterface
    {
        return self::getContainer()->get('doctrine')->getManager();
    }

    private function shipmentsDataProvider(): array
    {
        return [
            '100km' => [
                'company' => [
                    'name'  => 'HBO',
                    'email' => 'info@hbo.com'
                ],
                'carrier' => [
                    'name'  => 'OBH',
                    'email' => 'info@obh.com'
                ],
                'route'   => [
                    [
                        'postcode' => '16573',
                        'city'     => 'Berlin',
                        'country'  => 'DE'
                    ],
                    [
                        'postcode' => '19873',
                        'city'     => 'Köln',
                        'country'  => 'DE'
                    ]
                ],
                "price"   => 30.0
            ],
        ];
    }
}
