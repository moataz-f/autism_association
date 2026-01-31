<?php

namespace App\Repository;

use App\Entity\Seance;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Seance>
 */
class SeanceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Seance::class);
    }

    public function findOverlappingSessions($specialiste, $date, $heureDebut, $heureFin, $excludeId = null)
    {
        $qb = $this->createQueryBuilder('s')
            ->where('s.specialiste = :specialiste')
            ->andWhere('s.date = :date')
            ->andWhere('s.heureDebut < :heureFin')
            ->andWhere('s.heureFin > :heureDebut')
            ->setParameter('specialiste', $specialiste)
            ->setParameter('date', $date)
            ->setParameter('heureDebut', $heureDebut)
            ->setParameter('heureFin', $heureFin);

        if ($excludeId) {
            $qb->andWhere('s.id != :id')
               ->setParameter('id', $excludeId);
        }

        return $qb->getQuery()->getResult();
    }
}
