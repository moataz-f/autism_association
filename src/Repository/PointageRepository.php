<?php

namespace App\Repository;

use App\Entity\Pointage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Pointage>
 */
class PointageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pointage::class);
    }

    public function findTodayPointageByPersonnel($personnel)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.personnel = :val')
            ->andWhere('p.date = :today')
            ->setParameter('val', $personnel)
            ->setParameter('today', new \DateTime('today'))
            ->getQuery()
            ->getOneOrNullResult();
    }
}
