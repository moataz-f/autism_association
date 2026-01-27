<?php
namespace App\Repository;

use App\Entity\PageContent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PageContent>
 */
class PageContentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PageContent::class);
    }

    /**
     * Récupérer le contenu par section
     */
    public function findBySectionKey(string $sectionKey): ?PageContent
    {
        return $this->findOneBy([
            'sectionKey' => $sectionKey,
            'actif' => true
        ]);
    }

    /**
     * Récupérer tous les contenus actifs
     */
    public function findAllActive(): array
    {
        return $this->findBy(['actif' => true], ['sectionKey' => 'ASC']);
    }
}