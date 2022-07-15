<?php

declare(strict_types=1);

namespace App\Doctrine;

use App\Entity\Carrier;
use App\Entity\Company;
use App\Entity\Location;
use App\Entity\Shipment;
use Doctrine\ORM\EntityManagerInterface;

class ShipmentPrePersistListener
{

    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function prePersist(Shipment $shipment): void
    {
        $this->setCompanyFromDB($shipment);
        $this->setCarrierFromDB($shipment);
        $this->setRouteFromDB($shipment);
    }

    public function setCompanyFromDB(Shipment $shipment): void
    {
        $company = $shipment->getCompany();
        if ($company) {
            $companyInDb = $this->entityManager->getRepository(Company::class)->findOneBy(
                ['name' => $company->getName(), 'email' => $company->getEmail()]
            );
            if ($companyInDb) {
                $shipment->setCompany($companyInDb);
            }
        }
    }

    private function setCarrierFromDB(Shipment $shipment): void
    {
        $carrier = $shipment->getCarrier();
        if ($carrier) {
            $carrierInDb = $this->entityManager->getRepository(Carrier::class)->findOneBy(
                ['name' => $carrier->getName(), 'email' => $carrier->getEmail()]
            );
            if ($carrierInDb) {
                $shipment->setCarrier($carrierInDb);
            }
        }
    }

    private function setRouteFromDB(Shipment $shipment): void
    {
        $route = $shipment->getRoute();
        foreach ($route as $location) {
            $locationInDB = $this->entityManager->getRepository(Location::class)->findOneBy([
                'postcode' => $location->getPostCode(),
                'city'     => $location->getCity(),
                'country'  => $location->getCountry(),
            ]);
            if ($locationInDB) {
                $shipment->removeRoute($location);
                $shipment->addRoute($locationInDB);
            }
        }
    }
}