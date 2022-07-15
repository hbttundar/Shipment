<?php

declare(strict_types=1);

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use ApiPlatform\Core\DataPersister\DataPersisterInterface;
use App\Entity\Shipment;
use App\Service\ShipmentPriceResolver;
use Psr\Log\LoggerInterface;
use Symfony\Component\VarDumper\VarDumper;


class ShipmentDataPersister implements ContextAwareDataPersisterInterface
{

    private DataPersisterInterface $defaultDataPersister;
    private ShipmentPriceResolver  $priceResolver;
    private LoggerInterface        $logger;

    public function __construct(
        DataPersisterInterface $defaultDataPersister,
        ShipmentPriceResolver $priceResolver,
        LoggerInterface $logger
    ) {
        $this->defaultDataPersister = $defaultDataPersister;
        $this->priceResolver        = $priceResolver;
        /**
         * @todo logger add to log every thing in log if we need not use at the moment
         */
        $this->logger = $logger;
    }

    /**
     * @param Shipment $data
     */
    public function supports($data, array $context = []): bool
    {
        return $data instanceof Shipment;
    }

    /**
     * @param Shipment $data
     */
    public function persist($data, array $context = []): void
    {
        if (($context['item_operation_name'] ?? null) === 'put') {
            $previous_data     = $context['previous_data'] ?? null;
            $this->persistPreviousDataIfInNewNotProvided($previous_data, $data);
            $data->setUpdatedAt(new \DateTimeImmutable());
        }
        if (($context['item_operation_name'] ?? null) === 'post' || ($context['collection_operation_name'] ?? null)=='post') {
            $this->priceResolver->resolveShipmentPrice($data);
        }
        $this->defaultDataPersister->persist($data);
    }

    /**
     * @param Shipment $data
     */
    public function remove($data, array $context = [])
    {
        /**
         * @todo code write here but if we need delete operation we should enabled it for resource
         *       at the moment this code never called
         */
        $this->defaultDataPersister->remove($data);
    }

    public function persistPreviousDataIfInNewNotProvided(Shipment $previous_data, Shipment $data): void
    {
        $carrier = $previous_data->getCarrier();
        $company = $previous_data->getCompany();
        $route   = $previous_data->getRoute();
        $isDistanceChanged = $previous_data->getDistance() !== $data->getDistance();
        if ($isDistanceChanged) {
            $this->priceResolver->resolveShipmentPrice($data);
        }
        if (!$data->getCompany()) {
            $data->setCompany($company);
        }
        if (!$data->getCarrier()) {
            $data->setCarrier($carrier);
        }
        if (!$data->getRoute()->isEmpty()) {
            foreach ($route as $location) {
                $data->addRoute($location);
            }
        }
    }
}