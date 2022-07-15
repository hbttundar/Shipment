<?php

declare(strict_types=1);

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use ApiPlatform\Core\DataPersister\DataPersisterInterface;
use App\Entity\Company;
use Psr\Log\LoggerInterface;


class CompanyDataPersister implements ContextAwareDataPersisterInterface
{

    private DataPersisterInterface $defaultDataPersister;
    private LoggerInterface        $logger;

    public function __construct(
        DataPersisterInterface $defaultDataPersister,
        LoggerInterface $logger
    ) {
        $this->defaultDataPersister = $defaultDataPersister;
        /**
         * @todo logger add to log every thing in log if we need not use at the moment
         */
        $this->logger               = $logger;
    }

    /**
     * @param Company $data
     */
    public function supports($data, array $context = []): bool
    {
        return $data instanceof Company;
    }

    /**
     * @param Company $data
     */
    public function persist($data, array $context = []): void
    {
        if (($context['item_operation_name'] ?? null) === 'put') {
            $data->setUpdatedAt(new \DateTimeImmutable());
        }
        $this->defaultDataPersister->persist($data);
    }

    public function remove($data, array $context = [])
    {
        /**
         * @todo code write here but if we need delete operation we should enabled it for resource
         *       at the moment this code never called
         */
        $this->defaultDataPersister->remove($data);
    }
}