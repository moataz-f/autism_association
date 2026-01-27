<?php
namespace App\Repository;

use App\Entity\GalleryImage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GalleryImage>
 */
class GalleryImageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GalleryImage::class);
    }

    /**
     * Récupérer toutes les images actives triées par ordre
     */
    public function findAllActive(): array
    {
        return $this->createQueryBuilder('g')
            ->where('g.actif = :actif')
            ->setParameter('actif', true)
            ->orderBy('g.ordre', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Récupérer les X premières images pour la galerie
     */
    public function findForGallery(int $limit = 6): array
    {
        return $this->createQueryBuilder('g')
            ->where('g.actif = :actif')
            ->setParameter('actif', true)
            ->orderBy('g.ordre', 'ASC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Récupérer le prochain numéro d'ordre disponible
     */
    public function getNextOrdre(): int
    {
        $result = $this->createQueryBuilder('g')
            ->select('MAX(g.ordre) as maxOrdre')
            ->getQuery()
            ->getSingleScalarResult();

        return ($result ?? 0) + 1;
    }
}