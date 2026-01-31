<?php

namespace App\Repository;

use App\Entity\DonationRequest;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DonationRequest>
 *
 * @method DonationRequest|null find($id, $lockMode = null, $lockVersion = null)
 * @method DonationRequest|null findOneBy(array $criteria, array $orderBy = null)
 * @method DonationRequest[]    findAll()
 * @method DonationRequest[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DonationRequestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DonationRequest::class);
    }

    public function add(DonationRequest $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(DonationRequest $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
