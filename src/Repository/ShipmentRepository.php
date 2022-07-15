<?php

namespace App\Repository;

use App\Entity\Shipment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\VarDumper\VarDumper;

/**
 * @extends ServiceEntityRepository<Shipment>
 *
 * @method Shipment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Shipment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Shipment[]    findAll()
 * @method Shipment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ShipmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Shipment::class);
    }

    public function add(Shipment $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Shipment $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findOneByCompanyName(string $name)
    {
        return $this->createQueryBuilder('shipment')
                    ->LeftJoin('shipment.company', 'company')
                    ->andWhere('company.name = :name')
                    ->setParameter('name', $name)
                    ->orderBy('shipment.id', 'ASC')
                    ->getQuery()
                    ->getOneOrNullResult();
    }
}
